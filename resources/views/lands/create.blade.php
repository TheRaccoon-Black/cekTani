@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-1 mb-0">
            <span class="text-muted fw-light">Manajemen Lahan /</span> Tambah Baru
        </h4>
        <a href="{{ route('lands.index') }}" class="btn btn-label-secondary">
            <span class="tf-icons bx bx-arrow-back me-1"></span> Kembali
        </a>
    </div>

    <form action="{{ route('lands.store') }}" method="POST" id="createForm">
        @csrf

        <div class="row h-100">
            <div class="col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Detail Informasi</h5>
                    </div>
                    <div class="card-body mt-4">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lahan <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-purchase-tag"></i></span>
                                <input type="text" name="name" class="form-control" placeholder="Contoh: Kebun Jagung Blok A" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Lokasi / Alamat</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-map"></i></span>
                                <textarea name="address" class="form-control" rows="2" placeholder="Nama desa atau jalan..."></textarea>
                            </div>
                        </div>

                        <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                            <i class="bx bx-info-circle me-2 fs-4"></i>
                            <div style="font-size: 0.8rem;">
                                Gambar area di peta kanan untuk menghitung luas otomatis.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Estimasi Luas (m²)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="area_size" id="area_size" class="form-control bg-light" placeholder="0.00" readonly>
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>

                        <input type="hidden" name="geojson_data" id="geojson_data">

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="bx bx-save me-1"></i> Simpan Lahan
                            </button>
                            <a href="{{ route('lands.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-8 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center border-bottom p-3">
                        <h5 class="mb-0 card-title"><i class="bx bx-map-alt me-2 text-primary"></i>Peta Lokasi</h5>

                        <div class="d-flex gap-3 text-xs">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-dot bg-warning me-1"></span> Lahan Tetangga
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-dot bg-primary me-1"></span> Lahan Baru
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0 position-relative">
                        <div id="map" style="height: 600px; width: 100%; z-index: 1;"></div>

                        <div class="position-absolute bottom-0 start-0 m-3 p-3 bg-white rounded shadow opacity-90 d-none d-md-block" style="z-index: 400; font-size: 0.8rem; max-width: 250px;">
                            <strong>Cara Menggambar:</strong>
                            <ol class="ps-3 mb-0 mt-1">
                                <li>Pilih tool <i class="bx bx-shape-polygon"></i> (Polygon) di kiri atas peta.</li>
                                <li>Klik titik-titik sudut lahan.</li>
                                <li>Klik titik awal lagi untuk menutup area.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

<script>
    // --- 1. INISIALISASI PETA ---
    // Menggunakan Google Maps Satellite Hybrid (lyrs=s,h)

    var map = L.map('map').setView([-2.5, 118.0], 5); // Default Indonesia Center

    L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(map);

    var existingLands = @json($existingLands);
    var referenceGroup = L.featureGroup();

    existingLands.forEach(function(land) {
        try {
            if (!land.geojson_data) return;
            var geoJsonData = typeof land.geojson_data === 'string' ? JSON.parse(land.geojson_data) : land.geojson_data;

            var layer = L.geoJSON(geoJsonData, {
                style: {
                    color: '#ffab00', // Kuning (Warning) Sneat
                    fillColor: '#ffab00',
                    fillOpacity: 0.2,
                    weight: 1,
                    dashArray: '5, 5'
                }
            });

            layer.bindPopup(`
                <div class="text-center">
                    <strong class="text-warning">${land.name}</strong><br>
                    <small>${land.area_size} m²</small>
                </div>
            `);
            layer.addTo(referenceGroup);

        } catch (e) { console.error("Skip land", e); }
    });

    referenceGroup.addTo(map);

    if (referenceGroup.getLayers().length > 0) {
        map.fitBounds(referenceGroup.getBounds(), { padding: [50, 50] });
    }

    // --- 3. LOGIKA GAMBAR BARU ---
    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    var drawControl = new L.Control.Draw({
        draw: {
            polygon: {
                allowIntersection: false,
                showArea: true,
                shapeOptions: { color: '#696cff' }
            },
            rectangle: false, circle: false, marker: false, circlemarker: false, polyline: false
        },
        edit: {
            featureGroup: drawnItems,
            remove: true
        }
    });
    map.addControl(drawControl);

    function updateLandData(layer) {
        var latlngs = layer.getLatLngs()[0];
        var area = L.GeometryUtil.geodesicArea(latlngs);
        document.getElementById('area_size').value = area.toFixed(2);

        var data = drawnItems.toGeoJSON();
        if (data.features.length > 0) {
            document.getElementById('geojson_data').value = JSON.stringify(data.features[0]);
        } else {
            document.getElementById('geojson_data').value = '';
        }
    }

    map.on(L.Draw.Event.CREATED, function (e) {
        var layer = e.layer;

        drawnItems.clearLayers();

        layer.setStyle({
            color: '#696cff',
            fillColor: '#696cff',
            fillOpacity: 0.5
        });

        drawnItems.addLayer(layer);
        updateLandData(layer);
    });

    map.on(L.Draw.Event.EDITED, function (e) {
        e.layers.eachLayer(function (layer) { updateLandData(layer); });
    });

    map.on(L.Draw.Event.DELETED, function (e) {
        document.getElementById('area_size').value = '';
        document.getElementById('geojson_data').value = '';
    });

</script>
@endsection
