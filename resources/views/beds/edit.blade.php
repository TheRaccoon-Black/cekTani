<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Bedengan: {{ $bed->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <form action="{{ route('beds.update', $bed->id) }}" method="POST">
                        @csrf
                        @method('PUT') <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Nama Bedengan</label>
                            <input type="text" name="name" value="{{ $bed->name }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Panjang (m)</label>
                                <input type="number" step="0.1" name="length" value="{{ $bed->length }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lebar (m)</label>
                                <input type="number" step="0.1" name="width" value="{{ $bed->width }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700">Kapasitas (Tanaman)</label>
                            <input type="number" name="max_capacity" value="{{ $bed->max_capacity }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 font-bold text-green-700">
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('sectors.beds.index', $bed->sector_id) }}" class="text-gray-600 hover:text-gray-900 font-medium">Batal</a>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md font-bold shadow hover:bg-blue-700">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
