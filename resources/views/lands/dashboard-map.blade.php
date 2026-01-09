<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Monitoring') }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div class="text-sm text-gray-500 uppercase font-bold">Total Lahan</div>
                        <div class="text-3xl font-bold text-blue-600">{{ $lands->count() }}</div>
                        <div class="text-xs text-gray-400">Unit Terdaftar</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                        <div class="text-sm text-gray-500 uppercase font-bold">Total Luas</div>
                        <div class="text-3xl font-bold text-green-600">{{ number_format($totalAreaHa, 2, ',', '.') }}</div>
                        <div class="text-xs text-gray-400">Hektar</div>
                    </div>
                    <div class="col-span-2 flex items-center justify-end">
                        <button onclick="resetZoom()" class="bg-gray-800 text-white px-4 py-2 rounded shadow hover:bg-gray-700">
                            ⟲ Reset Tampilan Peta
                        </button>
                    </div>
                </div>

                <div id="map" style="height: 500px; width: 100%; border-radius: 8px; border: 2px solid #e5e7eb;"></div>
                <p class="text-xs text-gray-500 mt-2 text-center">* Klik ikon di peta atau daftar di bawah untuk melihat detail.</p>
            </div>


            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Lahan</h3>
                    <input type="text" id="searchLand" placeholder="Cari nama lahan..." class="border border-gray-300 rounded px-3 py-1 text-sm focus:ring-blue-500 focus:border-blue-500 w-64">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="landList">
                    @forelse($lands as $land)
                        <div id="card-{{ $land->id }}" onclick="focusOnLand({{ $land->id }})"
                             class="land-card cursor-pointer border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-blue-500 transition-all bg-white">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-lg">{{ $land->name }}</h4>
                                    <p class="text-sm text-gray-500 mb-2 h-10 overflow-hidden">{{ Str::limit($land->address, 50) ?? 'Tidak ada alamat' }}</p>
                                </div>
                                <div class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded">
                                    {{ number_format($land->area_size, 0, ',', '.') }} m²
                                </div>
                            </div>
                            <div class="mt-3 flex justify-between items-center">
                                <span class="text-xs text-blue-600 font-semibold">Klik untuk lihat peta ↗</span>
                                <a href="{{ route('lands.show', $land->id) }}" class="text-xs text-gray-400 underline hover:text-gray-600">Detail Page</a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8 text-gray-500">
                            Belum ada data lahan.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        var map = L.map('map', { scrollWheelZoom: false }).setView([-2.5, 118.0], 5);

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri', maxZoom: 18
        }).addTo(map);

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
                    style: { color: '#facc15', weight: 3, fillColor: '#facc15', fillOpacity: 0.4 }
                });

                var popupContent = `
                    <div class="text-center">
                        <b>${land.name}</b><br>
                        <span class="text-xs">${land.area_size} m²</span><br>
                        <a href="/lands/${land.id}" class="text-blue-600 text-xs underline">Buka Detail</a>
                    </div>
                `;
                layer.bindPopup(popupContent);

                layer.on('mouseover', function() { this.setStyle({ color: '#00ffff', fillOpacity: 0.7 }); });
                layer.on('mouseout', function() { this.setStyle({ color: '#facc15', fillOpacity: 0.4 }); });

                layer.addTo(markers);
                layersMap[land.id] = layer;

            } catch (e) { console.error(e); }
        });

        markers.addTo(map);

        setTimeout(function() {
            if (markers.getLayers().length > 0) {
                map.fitBounds(markers.getBounds(), { padding: [50, 50] });
            }
            map.invalidateSize();
        }, 500);

        window.focusOnLand = function(id) {
            var layer = layersMap[id];
            if (!layer) return;

            document.getElementById('map').scrollIntoView({ behavior: 'smooth', block: 'center' });

            map.fitBounds(layer.getBounds(), { maxZoom: 17 });

            setTimeout(() => { layer.openPopup(); }, 800);

            document.querySelectorAll('.land-card').forEach(el => el.classList.remove('ring', 'ring-blue-500'));
            var card = document.getElementById('card-' + id);
            if(card) card.classList.add('ring', 'ring-2', 'ring-blue-500');
        };

        window.resetZoom = function() {
            if (markers.getLayers().length > 0) {
                map.fitBounds(markers.getBounds(), { padding: [50, 50] });
                map.closePopup();
            }
        };

        document.getElementById('searchLand').addEventListener('keyup', function() {
            var val = this.value.toLowerCase();
            document.querySelectorAll('.land-card').forEach(el => {
                var text = el.innerText.toLowerCase();
                el.style.display = text.includes(val) ? 'block' : 'none';
            });
        });
    </script>
</x-app-layout>
