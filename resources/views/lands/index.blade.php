<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Lahan Pertanian') }}
            </h2>
            <a href="{{ route('lands.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                + Tambah Lahan Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if($lands->isEmpty())
                        <div class="text-center py-10 text-gray-500">
                            <p>Belum ada data lahan yang tersimpan.</p>
                            <a href="{{ route('lands.create') }}" class="text-blue-500 hover:underline mt-2 inline-block">Mulai gambar lahan</a>
                        </div>
                    @else
                        <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="py-3 px-6">Nama Lahan</th>
                                        <th scope="col" class="py-3 px-6">Lokasi / Alamat</th>
                                        <th scope="col" class="py-3 px-6">Luas (m²)</th>
                                        <th scope="col" class="py-3 px-6">Dibuat Pada</th>
                                        <th scope="col" class="py-3 px-6 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lands as $land)
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">
                                                {{ $land->name }}
                                            </td>
                                            <td class="py-4 px-6">
                                                {{Str::limit($land->address, 30) }}
                                            </td>
                                            <td class="py-4 px-6">
                                                {{ number_format($land->area_size, 0, ',', '.') }} m²
                                            </td>
                                            <td class="py-4 px-6">
                                                {{ $land->created_at->format('d M Y') }}
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                <a href="{{ route('lands.show', $land->id) }}" class="font-medium text-blue-600 hover:underline">
                                                    Lihat Peta
                                                </a>
                                                <a href="{{ route('lands.edit', $land->id) }}" class="text-yellow-600 hover:underline">Edit</a>

                                                <form action="{{ route('lands.destroy', $land->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus lahan ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $lands->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
