<div>
    <div class="md:flex md:items-center md:justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Laporan Absensi</h1>
        
        <div class="flex flex-wrap items-center gap-3 mt-4 md:mt-0">
            <select wire:model="selectedMonth" class="border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}</option>
                @endforeach
            </select>

            <select wire:model="selectedYear" class="border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500">
                @foreach($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
            
            <button wire:click="generateReport" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700 transition">
                Tampilkan
            </button>
            
            <button wire:click="printReport" class="bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md hover:bg-gray-800 transition flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak PDF
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Guru</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIDN</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sakit</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Izin</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Hadir (Fisik)</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tepat Waktu</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Telat</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($laporanData as $row)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $row['name'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $row['nidn'] ?? '-' }}</td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($row['total_sakit'] > 0)
                                <span class="px-2 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700">{{ $row['total_sakit'] }}</span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($row['total_izin'] > 0)
                                <span class="px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">{{ $row['total_izin'] }}</span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-gray-800">{{ $row['total_hadir'] }}</td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-center text-green-600 font-medium">{{ $row['total_tepat_waktu'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-red-600 font-medium">{{ $row['total_telat'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data absensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>