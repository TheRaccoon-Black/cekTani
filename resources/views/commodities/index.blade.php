<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Master Data Komoditas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Katalog Tanaman
                            </h3>

                            @if(session('success'))
                                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 text-sm text-green-700">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="overflow-x-auto border rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Komoditas</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Varietas</th>
                                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Estimasi Panen</th>
                                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($commodities as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-800">
                                                {{ $item->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $item->variety ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $item->harvest_duration_days }} Hari
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('commodities.edit', $item->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Edit</a>
                                                <form action="{{ route('commodities.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">
                                                Belum ada data komoditas.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200 sticky top-6">
                        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 rounded-t-lg">
                            <h3 class="text-white font-bold text-sm uppercase flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Tambah Baru
                            </h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('commodities.store') }}" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Nama Tanaman</label>
                                    <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Misal: Cabai Merah">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Varietas (Opsional)</label>
                                    <input type="text" name="variety" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Misal: Lado F1">
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700">Estimasi Masa Panen</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="number" name="harvest_duration_days" required min="1" class="block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 pr-12" placeholder="90">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Hari</span>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Dihitung sejak tanggal tanam.</p>
                                </div>

                                <button type="submit" class="w-full bg-green-600 text-white font-bold py-2 px-4 rounded-md hover:bg-green-700 transition shadow">
                                    Simpan Data
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout> 
