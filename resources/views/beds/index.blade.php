<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen Bedengan: <span class="text-blue-600 font-bold">{{ $sector->name }}</span>
            </h2>
            <span class="text-sm text-gray-500 mt-2 md:mt-0">
                Lokasi: {{ $sector->land->name }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6 flex items-center">
                <a href="{{ route('lands.map_sectors', $sector->land_id) }}"
                   class="group inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2 text-gray-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Peta
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    Daftar Bedengan Aktif
                                </h3>
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                    Total: {{ $sector->beds->count() }}
                                </span>
                            </div>

                            @if(session('success'))
                                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-start">
                                    <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            @endif

                            @if($sector->beds->count() > 0)
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Bedengan</th>
                                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Dimensi</th>
                                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kapasitas</th>
                                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($sector->beds as $bed)
                                            <tr class="hover:bg-blue-50 transition duration-150 ease-in-out">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-bold text-gray-900">{{ $bed->name }}</div>
                                                    <div class="text-xs text-gray-400">ID: #{{ $bed->id }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $bed->length }}m &times; {{ $bed->width }}m
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 text-green-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span class="text-sm font-bold text-green-700">{{ $bed->max_capacity }} Pohon</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="{{ route('beds.edit', $bed->id) }}" class="text-blue-600 hover:text-blue-900 mr-3 inline-block" title="Edit Data">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
    </a>
                                                    <form action="{{ route('beds.destroy', $bed->id) }}" method="POST" onsubmit="return confirm('Hapus bedengan {{ $bed->name }}? Data tidak bisa dikembalikan.');" class="inline-block">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition p-2 rounded-full hover:bg-red-50" title="Hapus Bedengan">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                    <div class="bg-white p-3 rounded-full shadow-sm mb-3">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900">Belum ada bedengan</h3>
                                    <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan bedengan baru di form sebelah kanan.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="sticky top-6"> <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                                <h3 class="text-white font-bold text-sm uppercase tracking-wide flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Tambah Bedengan
                                </h3>
                            </div>

                            <div class="p-6">
                                <form action="{{ route('beds.store', $sector->id) }}" method="POST">
                                    @csrf

                                    <div class="mb-5">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama / Kode Bedengan</label>
                                        <input type="text" name="name" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition sm:text-sm" placeholder="Contoh: Blok A-01">
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 mb-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Panjang (m)</label>
                                            <div class="relative rounded-md shadow-sm">
                                                <input type="number" step="0.1" id="input_panjang" name="length" required class="block w-full rounded-md border-gray-300 pr-8 focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="0">
                                                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                                    <span class="text-gray-400 sm:text-xs">m</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Lebar (m)</label>
                                            <div class="relative rounded-md shadow-sm">
                                                <input type="number" step="0.1" id="input_lebar" name="width" required class="block w-full rounded-md border-gray-300 pr-8 focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="0">
                                                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                                                    <span class="text-gray-400 sm:text-xs">m</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-yellow-50 rounded-lg border border-yellow-200 p-4 mb-6 shadow-sm">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-xs font-bold text-yellow-800 uppercase flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 3.666V14h-6v-3.334H9V7z" /></svg>
                                                Kalkulator Populasi
                                            </span>
                                        </div>

                                        <div class="flex items-end gap-2">
                                            <div class="w-full">
                                                <label class="text-[10px] text-gray-600 font-medium uppercase">Jarak Tanam (cm)</label>
                                                <input type="number" id="jarak_tanam" class="block w-full rounded border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-sm h-9" placeholder="50">
                                            </div>
                                            <button type="button" onclick="hitungPopulasi()" class="bg-yellow-400 text-yellow-900 hover:bg-yellow-500 px-4 h-9 rounded font-bold text-xs shadow-sm transition border border-yellow-500 flex items-center justify-center">
                                                Hitung
                                            </button>
                                        </div>
                                        <p class="text-[10px] text-yellow-700 mt-2 italic">*Hanya alat bantu hitung, tidak wajib.</p>
                                    </div>

                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas Maksimal</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                            </div>
                                            <input type="number" id="input_kapasitas" name="max_capacity" required class="block w-full pl-10 pr-12 rounded-md border-gray-300 focus:ring-green-500 focus:border-green-500 text-green-700 font-bold text-lg bg-gray-50" placeholder="0">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-xs font-medium">Tanaman</span>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition transform active:scale-95">
                                        Simpan Data Bedengan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function hitungPopulasi() {
            let panjangM = parseFloat(document.getElementById('input_panjang').value) || 0;
            let lebarM = parseFloat(document.getElementById('input_lebar').value) || 0;
            let jarakCm = parseFloat(document.getElementById('jarak_tanam').value) || 0;

            if (panjangM <= 0 || lebarM <= 0 || jarakCm <= 0) {
                alert("Harap isi Panjang, Lebar, dan Jarak Tanam dengan angka valid.");
                return;
            }

            // Hitungan: Luas Bedengan (cm2) / Luas per Tanaman (cm2)
            let luasBedenganCm = (panjangM * 100) * (lebarM * 100);
            let luasPerTanamanCm = jarakCm * jarakCm;
            let populasi = Math.floor(luasBedenganCm / luasPerTanamanCm);

            const inputKapasitas = document.getElementById('input_kapasitas');
            inputKapasitas.value = populasi;

            inputKapasitas.classList.remove('bg-gray-50');
            inputKapasitas.classList.add('bg-green-100', 'ring-2', 'ring-green-500');

            setTimeout(() => {
                inputKapasitas.classList.remove('bg-green-100', 'ring-2', 'ring-green-500');
                inputKapasitas.classList.add('bg-gray-50');
            }, 800);
        }
    </script>
</x-app-layout>
