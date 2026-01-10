@extends('layouts.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold py-1 mb-0">
                <span class="text-muted fw-light">Manajemen Lahan /</span> Mapping & Vegetasi
            </h4>
            <div class="text-muted small">
                Kontrol area dan monitoring tanaman di: <strong class="text-primary">{{ $land->name }}</strong>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('lands.index') }}" class="btn btn-label-secondary">
                <span class="tf-icons bx bx-arrow-back me-1"></span> Kembali
            </a>
        </div>
    </div>

    <div class="row h-100 mb-4">
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center border-bottom p-3">
                    <h5 class="mb-0 card-title"><i class="bx bx-map-alt me-2 text-primary"></i>Peta Sektor</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-label-primary"><i class="bx bx-leaf me-1"></i> {{ number_format($totalActivePlants) }} Tanaman Aktif</span>
                    </div>
                </div>
                <div class="card-body p-0 position-relative bg-light">
                    <div id="map" style="height: 550px; width: 100%; z-index: 1;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4 bg-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="d-block text-white text-opacity-75 text-uppercase fs-tiny fw-bold">Sisa Area</span>
                            <h3 class="mb-0 text-white">{{ number_format($remainingArea, 0, ',', '.') }} <small class="fs-6">m²</small></h3>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-white bg-opacity-25"><i class="bx bx-pie-chart-alt text-white"></i></span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between text-white text-opacity-75 small mb-1">
                        <span>Terpakai: {{ number_format($usedArea, 0, ',', '.') }} m²</span>
                        <span>{{ number_format($usagePercentage, 1) }}%</span>
                    </div>
                    <div class="progress bg-white bg-opacity-25" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: {{ $usagePercentage }}%"></div>
                    </div>
                </div>
            </div>

            <div class="card" style="height: 380px;">
                <div class="card-header border-bottom p-2">
                    <ul class="nav nav-pills nav-fill" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="tab-list" onclick="switchTab('list')"><i class="bx bx-list-ul me-1"></i> Data</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="tab-form" onclick="switchTab('form')"><i class="bx bx-pencil me-1"></i> Gambar</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0 d-flex flex-column h-100 overflow-hidden">
                    <div id="content-list" class="flex-grow-1 overflow-auto">
                        @if($land->sectors->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($land->sectors as $sector)
                                <div class="list-group-item list-group-item-action p-3 hover-bg-light" onclick="zoomToSector({{ $sector->id }})" style="cursor: pointer;">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 fw-bold text-primary">{{ $sector->name }}</h6>
                                        <small class="text-muted">{{ number_format($sector->area_size, 0) }} m²</small>
                                    </div>

                                    @php
                                        $activeCount = 0;
                                        $commodityName = '-';
                                        foreach($sector->beds as $bed) {
                                            if($bed->activePlantingCycle) {
                                                $activeCount += $bed->activePlantingCycle->current_plant_count;
                                                $commodityName = $bed->activePlantingCycle->commodity->name;
                                            }
                                        }
                                    @endphp

                                    @if($activeCount > 0)
                                        <div class="d-flex align-items-center mt-1">
                                            <span class="badge bg-label-success fs-tiny me-2">{{ $commodityName }}</span>
                                            <small class="text-muted fs-tiny">{{ $activeCount }} Pohon</small>
                                        </div>
                                    @else
                                        <small class="text-muted fs-tiny fst-italic">Belum ada tanaman aktif</small>
                                    @endif

                                    <div class="d-flex gap-2 mt-2 pt-2 border-top">
                                        <a href="{{ route('sectors.beds.index', $sector->id) }}" class="btn btn-xs btn-outline-primary flex-grow-1">Kelola</a>
                                        <form action="{{ route('sectors.destroy', $sector->id) }}" method="POST" onsubmit="return confirm('Hapus?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-icon btn-label-danger"><i class="bx bx-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5"><small class="text-muted">Belum ada sektor.</small></div>
                        @endif
                    </div>

                    <div id="content-form" class="hidden flex-grow-1 p-4">
                        <form action="{{ route('sectors.store', $land->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Nama Blok</label>
                                <input type="text" name="name" class="form-control" placeholder="Blok A" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Luas (m²)</label>
                                <input type="text" id="display_area" readonly class="form-control bg-light" placeholder="Gambar dulu...">
                            </div>
                            <input type="hidden" name="geojson_data" id="geojson_input">
                            <input type="hidden" name="area_size" id="area_input">
                            <button type="submit" id="btnSubmit" disabled class="btn btn-primary w-100">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Status Produksi per Sektor</h5>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Sektor</th>
                                <th>Komoditas</th>
                                <th>Populasi</th>
                                <th>Estimasi Panen</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($land->sectors as $sector)
                                @php
                                    $hasPlant = false;
                                    $mainCrop = '-';
                                    $population = 0;
                                    $harvestDate = null;

                                    foreach($sector->beds as $bed) {
                                        if($c = $bed->activePlantingCycle) {
                                            $hasPlant = true;
                                            $mainCrop = $c->commodity->name;
                                            $population += $c->current_plant_count;
                                            if(is_null($harvestDate) || $c->estimated_harvest_date < $harvestDate) {
                                                $harvestDate = $c->estimated_harvest_date;
                                            }
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td><strong>{{ $sector->name }}</strong></td>
                                    <td>
                                        @if($hasPlant)
                                            <span class="badge bg-label-success">{{ $mainCrop }}</span>
                                        @else
                                            <span class="badge bg-label-secondary">Kosong</span>
                                        @endif
                                    </td>
                                    <td>{{ $hasPlant ? number_format($population) . ' Pht' : '-' }}</td>
                                    <td>
                                        @if($harvestDate)
                                            {{ \Carbon\Carbon::parse($harvestDate)->format('d M Y') }}
                                            <br><small class="text-muted">({{ \Carbon\Carbon::parse($harvestDate)->diffForHumans() }})</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($hasPlant)
                                            <div class="progress" style="height: 6px; width: 80px;">
                                                <div class="progress-bar bg-success" style="width: {{ rand(20, 80) }}%"></div> </div>
                                        @else
                                            <span class="text-muted fs-tiny">Tidak aktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">Belum ada data sektor.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Distribusi Tanaman</h5>
                </div>
                <div class="card-body">
                    @if(count($commoditySummary) > 0)
                        <ul class="p-0 m-0">
                            @foreach($commoditySummary as $name => $count)
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-leaf"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{ $name }}</h6>
                                        <small class="text-muted">Sedang Ditanam</small>
                                    </div>
                                    <div class="user-progress">
                                        <small class="fw-semibold">{{ number_format($count) }}</small>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bx bx-loader-circle fs-1 mb-2"></i>
                            <p>Lahan belum berproduksi.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://unpkg.com/@turf/turf/turf.min.js"></script>

<style>
    .hidden { display: none !important; }
    .leaflet-popup-content { margin: 10px; text-align: center; }
</style>

<script>
    const landGeoJson = {!! json_encode($land->geojson_data) !!};

    const sectors = [
        @foreach($land->sectors as $s)
        {
            id: {{ $s->id }},
            name: "{{ $s->name }}",
            geojson: {!! $s->geojson_data !!},
            area: "{{ number_format($s->area_size, 0, ',', '.') }}",
            url: "{{ route('sectors.beds.index', $s->id) }}",
            @php
                $crop = null;
                foreach($s->beds as $b) {
                    if($b->activePlantingCycle) {
                        $crop = $b->activePlantingCycle->commodity->name; break;
                    }
                }
            @endphp
            crop: "{{ $crop ?? '' }}"
        },
        @endforeach
    ];

    const map = L.map('map', { zoomControl: false });
    L.control.zoom({ position: 'topright' }).addTo(map);
    L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{ maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3'] }).addTo(map);

    const landLayer = L.geoJSON(landGeoJson, { style: { color: '#fff', weight: 2, dashArray: '5,5', fillOpacity: 0 } }).addTo(map);
    map.fitBounds(landLayer.getBounds());

    const sectorLayers = {};
    const drawnItems = new L.FeatureGroup().addTo(map);

    function stringToColor(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) hash = str.charCodeAt(i) + ((hash << 5) - hash);
        let c = (hash & 0x00FFFFFF).toString(16).toUpperCase();
        return '#' + "00000".substring(0, 6 - c.length) + c;
    }

    sectors.forEach(s => {
        if(s.geojson) {
            const color = stringToColor(s.name);
            const fillOp = s.crop ? 0.6 : 0.3;

            const layer = L.geoJSON(s.geojson, {
                style: { color: color, weight: 2, fillColor: color, fillOpacity: fillOp }
            });

            let statusBadge = s.crop
                ? `<span class="badge bg-success mb-2">🌽 ${s.crop}</span>`
                : `<span class="badge bg-secondary mb-2">Kosong</span>`;

            const popupHtml = `
                <div>
                    <h6 class="mb-1 text-primary">${s.name}</h6>
                    ${statusBadge}<br>
                    <small>${s.area} m²</small><br>
                    <a href="${s.url}" class="btn btn-xs btn-primary mt-2">Lihat Detail</a>
                </div>
            `;

            layer.bindPopup(popupHtml).addTo(map);
            sectorLayers[s.id] = layer;
        }
    });

    const drawControl = new L.Control.Draw({
        draw: { polygon: { allowIntersection: false, showArea: true, shapeOptions: { color: '#ffab00' } }, marker: false, circle: false, circlemarker: false, polyline: false, rectangle: true },
        edit: { featureGroup: drawnItems, remove: true }
    });
    map.addControl(drawControl);

    map.on(L.Draw.Event.CREATED, function (e) {
        switchTab('form');
        const layer = e.layer;
        const geojson = layer.toGeoJSON();
        if (!turf.booleanContains(landGeoJson, geojson)) { alert("⚠️ Area keluar batas lahan!"); return; }
        drawnItems.clearLayers(); drawnItems.addLayer(layer);
        const area = turf.area(geojson);
        document.getElementById('display_area').value = new Intl.NumberFormat('id-ID').format(area.toFixed(2)) + " m²";
        document.getElementById('area_input').value = area.toFixed(2);
        document.getElementById('geojson_input').value = JSON.stringify(geojson);
        const btn = document.getElementById('btnSubmit');
        btn.disabled = false;
        btn.innerHTML = "Simpan";
    });

    map.on(L.Draw.Event.DELETED, function() {
        document.getElementById('btnSubmit').disabled = true;
        document.getElementById('display_area').value = "";
    });

    // Helpers
    window.zoomToSector = function(id) {
        const layer = sectorLayers[id];
        if(layer) { map.fitBounds(layer.getBounds()); layer.openPopup(); }
    };
    window.switchTab = function(tab) {
        if(tab === 'list') {
            document.getElementById('content-list').classList.remove('hidden');
            document.getElementById('content-form').classList.add('hidden');
            document.getElementById('tab-list').classList.add('active');
            document.getElementById('tab-form').classList.remove('active');
        } else {
            document.getElementById('content-list').classList.add('hidden');
            document.getElementById('content-form').classList.remove('hidden');
            document.getElementById('tab-form').classList.add('active');
            document.getElementById('tab-list').classList.remove('active');
        }
    };
</script>
@endsection
