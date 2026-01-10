<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Riwayat Tanam: <span class="text-blue-600">{{ $bed->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('sectors.beds.index', $bed->sector_id) }}" class="text-gray-500 hover:text-blue-600 font-medium transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Daftar Bedengan
                </a>
                <span class="text-sm text-gray-400">
                    Lokasi: {{ $bed->sector->land->name }} > {{ $bed->sector->name }}
                </span>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Arsip Siklus Tanam
                    </h3>

                    @if($bed->plantingCycles->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanaman</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Periode Tanam</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Durasi</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Jumlah Tanaman</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($bed->plantingCycles as $cycle)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">{{ $cycle->commodity->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $cycle->commodity->variety ?? 'Varietas -' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($cycle->start_date)->translatedFormat('d M Y') }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                s/d {{ \Carbon\Carbon::parse($cycle->updated_at)->translatedFormat('d M Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $start = \Carbon\Carbon::parse($cycle->start_date);
                                                $end = \Carbon\Carbon::parse($cycle->updated_at); // Saat dipanen
                                                $durasi = $start->diffInDays($end);
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $durasi }} Hari
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            {{ $cycle->initial_plant_count }} Pohon
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <p class="text-gray-500">Belum ada riwayat panen di bedengan ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
