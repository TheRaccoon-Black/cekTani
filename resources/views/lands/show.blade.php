<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Lahan: ') . $land->name }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6">
                        <p><strong>Nama Pemilik/Lahan:</strong> {{ $land->name }}</p>
                        <p><strong>Luas / Deskripsi:</strong> {{ $land->area_size ?? '-' }}</p>
                    </div>

                    <div id="map" style="height: 500px; width: 100%; border-radius: 8px; z-index: 1;"></div>

                    <div class="mt-4">
                        <a href="{{ route('lands.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
    var map = L.map('map').setView([-3.440303, 102.238888], 13);

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri, DigitalGlobe, Earthstar Geographics',
            maxZoom: 18
        }).addTo(map);

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri',
            maxZoom: 18
        }).addTo(map);

    var rawData = @json($land->geojson_data);

    console.log("Data GeoJSON:", rawData);

    try {
        var geoJsonData = typeof rawData === 'string' ? JSON.parse(rawData) : rawData;

        var layer = L.geoJSON(geoJsonData, {
            style: {
                color: 'blue',
                fillColor: '#30f',
                fillOpacity: 0.4
            }
        }).addTo(map);

        layer.bindPopup("<b>{{ $land->name }}</b><br>Luas: {{ $land->area_size }} m²");

        map.fitBounds(layer.getBounds());

    } catch (error) {
        console.error("Error menampilkan peta:", error);
        alert("Gagal memproses data GeoJSON.");
    }
</script>
</x-app-layout>
