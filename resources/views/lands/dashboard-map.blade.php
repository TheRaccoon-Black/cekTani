@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Operasional /</span> Monitoring Peta Global
    </h4>

    <div class="row">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sebaran Lahan</h5>
                    <button onclick="resetZoom()" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-reset me-1"></i> Reset Zoom
                    </button>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 500px; width: 100%; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4 order-1">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                                <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                                    <div class="card-title">
                                        <h5 class="text-nowrap mb-2">Total Aset Lahan</h5>
                                        <span class="badge bg-label-primary rounded-pill">Aktif</span>
                                    </div>
                                    <div class="mt-sm-auto">
                                        <h3 class="mb-0">{{ $lands->count() }} Unit</h3>
                                    </div>
                                </div>
                                <div id="profileReportChart"></div>
                                <div class="avatar avatar-lg">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="bx bx-map-pin fs-1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                                <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                                    <div class="card-title">
                                        <h5 class="text-nowrap mb-2">Total Luas Area</h5>
                                        <span class="badge bg-label-success rounded-pill">Produktif</span>
                                    </div>
                                    <div class="mt-sm-auto">
                                        <h3 class="mb-0">{{ number_format($totalAreaHa, 2, ',', '.') }} Ha</h3>
                                        <small class="text-success text-nowrap fw-semibold">
                                            ({{ number_format($totalAreaM2, 0, ',', '.') }} m²)
                                        </small>
                                    </div>
                                </div>
                                <div class="avatar avatar-lg">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="bx bx-area fs-1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title text-white">Tips Navigasi</h5>
                            <p class="card-text">
                                Klik pada area berwarna kuning di peta untuk melihat detail lahan, atau pilih dari daftar di bawah.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between gap-3">
            <h5 class="card-title mb-0">Navigasi Cepat Lahan</h5>
            <div class="d-flex align-items-center">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" id="searchLand" class="form-control" placeholder="Cari nama lahan..." aria-label="Cari nama lahan...">
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-4" id="landList">
                @forelse($lands as $land)
                    <div class="col-md-6 col-lg-4 land-card-item">
                        <div id="card-{{ $land->id }}" class="card h-100 border cursor-pointer hover-card shadow-none" onclick="focusOnLand({{ $land->id }})">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-info">
                                            <i class="bx bx-landscape"></i>
                                        </span>
                                    </div>
                                    <h5 class="mb-0 text-truncate">{{ $land->name }}</h5>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted text-xs text-uppercase">Luas Area</span>
                                        <span class="fw-bold">{{ number_format($land->area_size, 0, ',', '.') }} m²</span>
                                    </div>
                                    <div class="d-flex flex-column text-end">
                                        <span class="text-muted text-xs text-uppercase">Lokasi</span>
                                        <small class="text-truncate" style="max-width: 120px;">
                                            {{ $land->address ?? 'N/A' }}
                                        </small>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                    <span class="text-primary text-xs fw-bold cursor-pointer">
                                        <i class="bx bx-target-lock me-1"></i> Lokasi Peta
                                    </span>
                                    <a href="{{ route('lands.map_sectors', $land->id) }}" class="btn btn-xs btn-label-secondary">
                                        Detail <i class="bx bx-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="mb-3">
                            <span class="avatar avatar-xl rounded-circle bg-label-secondary p-4">
                                <i class="bx bx-map-alt fs-1"></i>
                            </span>
                        </div>
                        <h5 class="mb-1">Belum ada data lahan</h5>
                        <p class="text-muted">Tambahkan lahan baru untuk melihatnya di peta.</p>
                        <a href="{{ route('lands.create') }}" class="btn btn-primary">Tambah Lahan</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .hover-card:hover {
        border-color: #696cff !important;
        background-color: #f8f9fa;
        transform: translateY(-2px);
        transition: all 0.2s ease-in-out;
    }
    .card-active {
        border-color: #696cff !important;
        background-color: #f0f1ff !important;
        box-shadow: 0 0.25rem 1rem rgba(105, 108, 255, 0.2);
    }
</style>

<script>
    // --- 1. INISIALISASI PETA ---
    var map = L.map('map', { scrollWheelZoom: false }).setView([-2.5, 118.0], 5);

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri', maxZoom: 18
    }).addTo(map);

    // Menambahkan Label Jalan (Hybrid) agar lebih jelas
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri', maxZoom: 18
    }).addTo(map);

    // --- 2. RENDER DATA ---
    var allLands = @json($lands);
    var markers = L.featureGroup();
    var layersMap = {};

    allLands.forEach(function(land) {
        try {
            var geoJsonData = typeof land.geojson_data === 'string' ? JSON.parse(land.geojson_data) : land.geojson_data;
            if (!geoJsonData) return;

            var layer = L.geoJSON(geoJsonData, {
                style: { color: '#ffab00', weight: 2, fillColor: '#ffab00', fillOpacity: 0.4 } // Warna Kuning/Orange Sneat
            });

            var popupContent = `
                <div class="text-center p-2">
                    <h6 class="mb-1 text-primary">${land.name}</h6>
                    <span class="badge bg-label-warning mb-2">${land.area_size.toLocaleString('id-ID')} m²</span><br>
                    <a href="/lands/${land.id}/map-sectors" class="btn btn-xs btn-primary text-white mt-1">Buka Detail</a>
                </div>
            `;
            layer.bindPopup(popupContent);

            // Hover effect pada Peta
            layer.on('mouseover', function() { this.setStyle({ color: '#00ffff', fillOpacity: 0.7, weight: 3 }); });
            layer.on('mouseout', function() { this.setStyle({ color: '#ffab00', fillOpacity: 0.4, weight: 2 }); });

            // Klik peta highlight kartu
            layer.on('click', function() {
               highlightCard(land.id);
            });

            layer.addTo(markers);
            layersMap[land.id] = layer;

        } catch (e) { console.error(e); }
    });

    markers.addTo(map);

    // Auto Zoom agar semua lahan terlihat
    setTimeout(function() {
        if (markers.getLayers().length > 0) {
            map.fitBounds(markers.getBounds(), { padding: [50, 50] });
        }
        map.invalidateSize();
    }, 500);

    // --- 3. FUNGSI INTERAKSI ---

    window.focusOnLand = function(id) {
        var layer = layersMap[id];
        if (!layer) return;

        // Scroll halus ke peta
        document.getElementById('map').scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Zoom ke lokasi
        map.fitBounds(layer.getBounds(), { maxZoom: 17 });

        setTimeout(() => { layer.openPopup(); }, 800);

        highlightCard(id);
    };

    function highlightCard(id) {
        // Hapus kelas aktif dari semua kartu
        document.querySelectorAll('.hover-card').forEach(el => el.classList.remove('card-active'));

        // Tambah kelas ke kartu yang dipilih
        var card = document.getElementById('card-' + id);
        if(card) {
            card.classList.add('card-active');
            // Scroll ke kartu jika perlu (opsional)
            // card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    window.resetZoom = function() {
        if (markers.getLayers().length > 0) {
            map.fitBounds(markers.getBounds(), { padding: [50, 50] });
            map.closePopup();
            document.querySelectorAll('.hover-card').forEach(el => el.classList.remove('card-active'));
        }
    };

    // --- 4. SEARCH FILTER ---
    document.getElementById('searchLand').addEventListener('keyup', function() {
        var val = this.value.toLowerCase();
        var items = document.querySelectorAll('.land-card-item');

        items.forEach(el => {
            var text = el.innerText.toLowerCase();
            if (text.includes(val)) {
                el.style.display = 'block';
                el.parentElement.classList.remove('d-none');
            } else {
                el.style.display = 'none';
            }
        });
    });
</script>
@endsection
