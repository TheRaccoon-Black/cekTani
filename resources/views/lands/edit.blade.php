@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-1 mb-0">
            <span class="text-muted fw-light">Manajemen Lahan /</span> Edit Data
        </h4>
        <a href="{{ route('lands.index') }}" class="btn btn-label-secondary">
            <span class="tf-icons bx bx-arrow-back me-1"></span> Kembali
        </a>
    </div>

    <form action="{{ route('lands.update', $land->id) }}" method="POST" id="editForm">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Informasi Lahan</h5>
                    </div>
                    <div class="card-body mt-4">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lahan <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-purchase-tag"></i></span>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $land->name) }}" placeholder="Contoh: Kebun Cabai A" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Lokasi / Alamat</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-map"></i></span>
                                <textarea name="address" class="form-control" rows="2" placeholder="Alamat lengkap...">{{ old('address', $land->address) }}</textarea>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="alert alert-primary mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-info-circle me-2 fs-4"></i>
                                <span>Luas dihitung otomatis dari peta.</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Luas Area (m²)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="area_size" id="area_size"
                                    value="{{ old('area_size', $land->area_size) }}"
                                    class="form-control bg-light" readonly>
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>

                        <input type="hidden" name="geojson_data" id="geojson_data"
                            value="{{ is_array($land->geojson_data) ? json_encode($land->geojson_data) : $land->geojson_data }}">

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('lands.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center border-bottom p-3">
                        <h5 class="mb-0 card-title"><i class="bx bx-map-alt me-2 text-primary"></i>Editor Peta</h5>
                        <span class="badge bg-label-warning"><i class="bx bx-pencil me-1"></i> Mode Edit</span>
                    </div>

                    <div class="card-body p-0 position-relative">
                        <div id="map" style="height: 600px; width: 100%; z-index: 1;"></div>

                        <div class="position-absolute top-0 end-0 m-3 p-2 bg-white rounded shadow opacity-90 d-none d-md-block" style="z-index: 400; font-size: 0.8rem;">
                            <strong>Kontrol Edit:</strong>
                            <ul class="list-unstyled mb-0 mt-1">
                                <li><i class="bx bx-edit"></i> Edit: Geser titik putih</li>
                                <li><i class="bx bx-trash"></i> Hapus: Klik tong sampah lalu klik area</li>
                            </ul>
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
    var map = L.map('map').setView([-3.440303, 102.238888], 13);

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri, DigitalGlobe', maxZoom: 18
    }).addTo(map);

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri', maxZoom: 18
    }).addTo(map);

    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    function calculateAndSetArea(layer) {
        var areaInput = document.getElementById('area_size');
        if (!layer || !areaInput) return;

        var latlngs = layer.getLatLngs()[0];
        var area = L.GeometryUtil.geodesicArea(latlngs);

        areaInput.value = area.toFixed(2);
    }

    function updateInput() {
        var data = drawnItems.toGeoJSON();
        if (data.features.length === 0) {
            document.getElementById('geojson_data').value = '';
            document.getElementById('area_size').value = 0;
        } else {
            document.getElementById('geojson_data').value = JSON.stringify(data.features[0]);
        }
    }

    var oldData = @json($land->geojson_data);

    try {
        var geoJsonData = typeof oldData === 'string' ? JSON.parse(oldData) : oldData;

        if (geoJsonData) {
            L.geoJSON(geoJsonData, {
                style: { color: '#ffab00', weight: 3, fillColor: '#ffab00', fillOpacity: 0.4 },
                onEachFeature: function (feature, layer) {
                    drawnItems.addLayer(layer);

                    if (layer instanceof L.Polygon) {
                        calculateAndSetArea(layer);
                    }
                }
            });

            if (drawnItems.getLayers().length > 0) {
                map.fitBounds(drawnItems.getBounds());
            }
        }
    } catch (e) {
        console.error("Gagal memuat data lama:", e);
    }

    var drawControl = new L.Control.Draw({
        edit: {
            featureGroup: drawnItems,
            remove: true
        },
        draw: {
            polygon: {
                allowIntersection: false,
                showArea: true,
                shapeOptions: { color: '#696cff' }
            },
            rectangle: false, circle: false, marker: false, circlemarker: false, polyline: false
        }
    });
    map.addControl(drawControl);
    map.on(L.Draw.Event.EDITED, function (e) {
        var layers = e.layers;
        layers.eachLayer(function (layer) {
            calculateAndSetArea(layer);
        });
        updateInput();
    });

    map.on(L.Draw.Event.CREATED, function (e) {
        var layer = e.layer;

        drawnItems.clearLayers();
        drawnItems.addLayer(layer);

        calculateAndSetArea(layer);
        updateInput();
    });

    map.on(L.Draw.Event.DELETED, function (e) {
        updateInput();
    });

</script>
@endsection
