<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Lahan Baru') }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('lands.store') }}" method="POST" id="createForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lahan</label>
                                <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required placeholder="Contoh: Kebun Jagung Blok A">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Alamat / Lokasi</label>
                                <input type="text" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Nama desa atau jalan">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Luas Lahan (m²)</label>
                                <input type="number" step="0.01" name="area_size" id="area_size" readonly
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm cursor-not-allowed">
                                <small class="text-gray-500">Gambar area di peta untuk menghitung luas otomatis.</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Lahan</label>

                            <div class="flex gap-4 mb-2 text-sm">
                                <div class="flex items-center"><span class="w-3 h-3 bg-yellow-500 inline-block mr-1 opacity-50 border border-yellow-600"></span> Lahan Tetangga (Ada)</div>
                                <div class="flex items-center"><span class="w-3 h-3 bg-blue-600 inline-block mr-1 opacity-50 border border-blue-600"></span> Lahan Baru (Sedang Digambar)</div>
                            </div>

                            <div id="map" style="height: 500px; width: 100%; border:1px solid #ccc; border-radius: 8px;"></div>
                        </div>

                        <input type="hidden" name="geojson_data" id="geojson_data">

                        <div class="flex justify-end">
                            <a href="{{ route('lands.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded mr-2">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Lahan</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    <script>
        var map = L.map('map').setView([-3.440303, 102.238888], 13); 

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri', maxZoom: 18
        }).addTo(map);
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri', maxZoom: 18
        }).addTo(map);

        var existingLands = @json($existingLands);
        var referenceGroup = L.featureGroup();
        existingLands.forEach(function(land) {
            try {
                if (!land.geojson_data) return;
                var geoJsonData = typeof land.geojson_data === 'string' ? JSON.parse(land.geojson_data) : land.geojson_data;

                var layer = L.geoJSON(geoJsonData, {
                    style: {
                        color: '#f1c40f',
                        fillColor: '#f1c40f',
                        fillOpacity: 0.3,
                        weight: 1,
                        dashArray: '5, 5'
                    }
                });

                layer.bindPopup(`<b>${land.name}</b><br>Luas: ${land.area_size} m²`);
                layer.addTo(referenceGroup);

            } catch (e) { console.error("Skip land", e); }
        });

        referenceGroup.addTo(map);

        if (referenceGroup.getLayers().length > 0) {
            map.fitBounds(referenceGroup.getBounds(), { padding: [50, 50] });
        }

        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            draw: {
                polygon: true,
                rectangle: true,
                circle: false, marker: false, circlemarker: false, polyline: false
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
                color: '#3388ff',
                fillColor: '#3388ff',
                fillOpacity: 0.5
            });

            drawnItems.addLayer(layer);
            updateLandData(layer);
        });

        // Event Edited
        map.on(L.Draw.Event.EDITED, function (e) {
            e.layers.eachLayer(function (layer) { updateLandData(layer); });
        });

        // Event Deleted
        map.on(L.Draw.Event.DELETED, function (e) {
            document.getElementById('area_size').value = '';
            document.getElementById('geojson_data').value = '';
        });

    </script>
</x-app-layout>
