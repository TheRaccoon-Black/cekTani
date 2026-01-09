<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mapping Area: {{ $land->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-4">
                <a href="{{ route('lands.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500">
                    &larr; Kembali ke Daftar Lahan
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-700">Visualisasi Lahan</h3>
                            <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border">Google Satellite Mode</span>
                        </div>

                        <div id="map" style="height: 600px; width: 100%; z-index: 1;"></div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 flex flex-col" style="height: 600px;">

                        <div class="p-4 bg-blue-50 border-b">
                            <h4 class="text-xs font-bold text-gray-500 uppercase">Status Penggunaan</h4>
                            <div class="flex justify-between items-end mt-1">
                                <div>
                                    <span class="text-2xl font-bold text-blue-700">{{ number_format($usedArea, 0, ',', '.') }}</span>
                                    <span class="text-xs text-gray-500">m² Terpakai</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-bold {{ $remainingArea < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        Sisa: {{ number_format($remainingArea, 0, ',', '.') }} m²
                                    </span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $usagePercentage }}%"></div>
                            </div>
                        </div>

                        <div class="flex border-b bg-gray-100">
                            <button onclick="switchTab('list')" id="tab-list" class="flex-1 py-3 text-sm font-bold text-blue-600 bg-white border-t-2 border-blue-600">
                                Daftar Sektor
                            </button>
                            <button onclick="switchTab('form')" id="tab-form" class="flex-1 py-3 text-sm font-medium text-gray-500 hover:bg-gray-50">
                                + Tambah Baru
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto p-4 relative">

                            <div id="content-list">
                                @if($land->sectors->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($land->sectors as $sector)
                                        <div class="p-3 border rounded-lg hover:bg-blue-50 cursor-pointer transition group bg-white shadow-sm"
                                             onclick="zoomToSector({{ $sector->id }})">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h5 class="font-bold text-gray-800">{{ $sector->name }}</h5>
                                                    <p class="text-xs text-gray-500">{{ number_format($sector->area_size, 0, ',', '.') }} m²</p>
                                                </div>
                                                <span class="text-blue-500 text-xs opacity-0 group-hover:opacity-100 font-bold">Lihat &rarr;</span>
                                            </div>
                                            <div class="mt-2 flex space-x-2 border-t pt-2">
                                                {{-- <a href="{{ route('sectors.beds.index', $sector->id) }}" class="flex-1 text-center text-xs bg-green-100 text-green-700 py-1 rounded hover:bg-green-200">
                                                    Kelola Bedengan
                                                </a> --}}
                                                <form action="{{ route('sectors.destroy', $sector->id) }}" method="POST" onsubmit="return confirm('Hapus sektor ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-10 text-gray-400">
                                        <p>Belum ada sektor.</p>
                                        <p class="text-xs">Klik tab "Tambah Baru" untuk menggambar.</p>
                                    </div>
                                @endif
                            </div>

                            <div id="content-form" class="hidden">
                                <form action="{{ route('sectors.store', $land->id) }}" method="POST" id="sectorForm">
                                    @csrf

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sektor / Blok</label>
                                        <input type="text" name="name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Blok Utara" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Luas Area (m²)</label>
                                        <input type="text" id="display_area" readonly class="w-full bg-gray-100 text-gray-500 rounded-md border-gray-300 shadow-sm cursor-not-allowed" placeholder="0.00">
                                        <p class="text-xs text-gray-500 mt-1">*Terisi otomatis setelah menggambar.</p>
                                    </div>

                                    <input type="hidden" name="geojson_data" id="geojson_input">
                                    <input type="hidden" name="area_size" id="area_input">

                                    <button type="submit" id="btnSubmit" disabled
                                            class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                        Simpan Sektor
                                    </button>

                                    <div class="mt-4 p-3 bg-yellow-50 rounded text-xs text-yellow-800 border border-yellow-200">
                                        <strong>Panduan:</strong>
                                        <ul class="list-disc pl-4 mt-1">
                                            <li>Gunakan icon <strong>Segi Lima</strong> di peta.</li>
                                            <li>Gambar area di dalam batas lahan.</li>
                                            <li>Data luas akan masuk otomatis.</li>
                                        </ul>
                                    </div>
                                </form>
                            </div>

                        </div>
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

    <script>
        const landGeoJson = {!! json_encode($land->geojson_data) !!};
        const sectors = {!! $land->sectors->map(function($s){
            return [
                'id' => $s->id,
                'name' => $s->name,
                'geojson' => json_decode($s->geojson_data)
            ];
        })->toJson() !!};

        const map = L.map('map', { zoomControl: false });
        L.control.zoom({ position: 'topright' }).addTo(map);

        L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{
            maxZoom: 20,
            subdomains:['mt0','mt1','mt2','mt3']
        }).addTo(map);

        const landLayer = L.geoJSON(landGeoJson, {
            style: { color: '#ffffff', weight: 3, dashArray: '10, 5', fillOpacity: 0.1 }
        }).addTo(map);
        map.fitBounds(landLayer.getBounds());

        const sectorLayers = {};

        function stringToColor(str) {
            let hash = 0;
            for (let i = 0; i < str.length; i++) hash = str.charCodeAt(i) + ((hash << 5) - hash);
            let c = (hash & 0x00FFFFFF).toString(16).toUpperCase();
            return '#' + "00000".substring(0, 6 - c.length) + c;
        }

        sectors.forEach(sector => {
            if(sector.geojson) {
                const color = stringToColor(sector.name);
                const layer = L.geoJSON(sector.geojson, {
                    style: { color: color, weight: 2, fillColor: color, fillOpacity: 0.5 }
                }).bindPopup(`<b>${sector.name}</b>`).addTo(map);

                sectorLayers[sector.id] = layer;
            }
        });

        window.zoomToSector = function(id) {
            const layer = sectorLayers[id];
            if(layer) {
                map.fitBounds(layer.getBounds());
                layer.openPopup();
            }
        };

        window.switchTab = function(tabName) {
            const listBtn = document.getElementById('tab-list');
            const formBtn = document.getElementById('tab-form');
            const listContent = document.getElementById('content-list');
            const formContent = document.getElementById('content-form');

            if(tabName === 'list') {
                listContent.classList.remove('hidden');
                formContent.classList.add('hidden');
                listBtn.classList.replace('text-gray-500', 'text-blue-600');
                listBtn.classList.add('border-t-2', 'border-blue-600', 'bg-white', 'font-bold');
                listBtn.classList.remove('hover:bg-gray-50', 'bg-gray-100', 'font-medium');

                formBtn.classList.replace('text-blue-600', 'text-gray-500');
                formBtn.classList.remove('border-t-2', 'border-blue-600', 'bg-white', 'font-bold');
                formBtn.classList.add('hover:bg-gray-50', 'bg-gray-100', 'font-medium');
            } else {
                listContent.classList.add('hidden');
                formContent.classList.remove('hidden');
                formBtn.classList.replace('text-gray-500', 'text-blue-600');
                formBtn.classList.add('border-t-2', 'border-blue-600', 'bg-white', 'font-bold');
                formBtn.classList.remove('hover:bg-gray-50', 'bg-gray-100', 'font-medium');

                listBtn.classList.replace('text-blue-600', 'text-gray-500');
                listBtn.classList.remove('border-t-2', 'border-blue-600', 'bg-white', 'font-bold');
                listBtn.classList.add('hover:bg-gray-50', 'bg-gray-100', 'font-medium');
            }
        };

        const drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        const drawControl = new L.Control.Draw({
            draw: {
                polygon: { allowIntersection: false, showArea: true, shapeOptions: { color: '#fbbf24' } },
                marker: false, circle: false, circlemarker: false, polyline: false, rectangle: true
            },
            edit: { featureGroup: drawnItems, remove: true }
        });
        map.addControl(drawControl);

        map.on(L.Draw.Event.CREATED, function (e) {
            switchTab('form');
            const layer = e.layer;
            const geojson = layer.toGeoJSON();

            if (!turf.booleanContains(landGeoJson, geojson)) {
                alert("Area keluar batas lahan!");
                return;
            }

            drawnItems.clearLayers();
            drawnItems.addLayer(layer);

            const area = turf.area(geojson);
            document.getElementById('display_area').value = area.toFixed(2);
            document.getElementById('area_input').value = area.toFixed(2);
            document.getElementById('geojson_input').value = JSON.stringify(geojson);

            const btn = document.getElementById('btnSubmit');
            btn.disabled = false;
            btn.innerText = "Simpan Sektor";
        });

        map.on(L.Draw.Event.DELETED, function(e) {
            document.getElementById('btnSubmit').disabled = true;
            document.getElementById('display_area').value = "";
        });

    </script>
</x-app-layout>
