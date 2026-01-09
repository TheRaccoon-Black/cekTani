<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Lahan: {{ $land->name }}</h2>
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('lands.update', $land->id) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lahan</label>
                                <input type="text" name="name" value="{{ old('name', $land->name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Alamat / Lokasi</label>
                                <input type="text" name="address" value="{{ old('address', $land->address) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Luas Lahan pada peta(m²)</label>
                                <input type="number" step="0.01" name="area_size" id="area_size"
                                    value="{{ old('area_size', $land->area_size) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm cursor-not-allowed">
                                <small class="text-gray-500">Luas akan terhitung otomatis berdasarkan gambar peta.</small>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ukuran tertulis</label>
                                <input type="text" readonly value="{{$land->area_size}} m²"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Area Lahan (Edit bentuk jika perlu)</label>
                            <div id="map" style="height: 500px; width: 100%; border:1px solid #ccc; border-radius: 8px;"></div>
                            <small class="text-gray-500">Gunakan tombol <b>Edit (ikon kotak pensil)</b> di peta untuk mengubah titik sudut.</small>
                        </div>

                        <input type="hidden" name="geojson_data" id="geojson_data"
                            value="{{ is_array($land->geojson_data) ? json_encode($land->geojson_data) : $land->geojson_data }}">

                        <div class="flex justify-end">
                            <a href="{{ route('lands.index') }}"
                                class="px-4 py-2 bg-gray-300 text-gray-800 rounded mr-2">Batal</a>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Perubahan</button>
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
            attribution: '© Esri, DigitalGlobe',
            maxZoom: 18
        }).addTo(map);

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri',
            maxZoom: 18
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
            } else {
                document.getElementById('geojson_data').value = JSON.stringify(data.features[0]);
            }
        }

        var oldData = @json($land->geojson_data);

        try {
            var geoJsonData = typeof oldData === 'string' ? JSON.parse(oldData) : oldData;

            L.geoJSON(geoJsonData, {
                onEachFeature: function (feature, layer) {
                    drawnItems.addLayer(layer);

                    if (layer instanceof L.Polygon) {
                        calculateAndSetArea(layer);
                    }
                }
            });

            if (drawnItems.getBounds().isValid()) {
                map.fitBounds(drawnItems.getBounds());
            }
        } catch (e) {
            console.error("Gagal memuat data lama", e);
        }

        var drawControl = new L.Control.Draw({
            edit: {
                featureGroup: drawnItems,
                remove: true
            },
            draw: {
                polygon: true,
                rectangle: false,
                circle: false,
                marker: false,
                circlemarker: false,
                polyline: false
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
            document.getElementById('area_size').value = 0;
        });

    </script>
</x-app-layout>
