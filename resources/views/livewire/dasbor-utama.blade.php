<div wire:poll.3s class="space-y-8">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-gray-500">Total Guru</div>
                <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-900">{{ $totalGuru }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-gray-500">Perangkat Online</div>
                <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-900">
                    <span class="text-green-600">{{ $perangkatOnline }}</span>
                    <span class="text-xl font-medium text-gray-500">/ {{ $totalPerangkat }}</span>
                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-gray-500">Total Hadir Hari Ini</div>
                <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-900">{{ $absensiHariIni }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-green-600 mb-4">Hadir Tepat Waktu ({{ $guruHadirTepatWaktu->count() }})</h3>
            <ul class="max-h-96 overflow-y-auto divide-y divide-gray-200">
                @forelse ($guruHadirTepatWaktu as $guru)
                    <li class="py-3 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <span class="font-semibold text-green-700">{{ strtoupper(substr($guru->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $guru->name }}</p>
                                <p class="text-sm text-gray-500">{{ $guru->nidn ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-sm text-green-600">
                                {{ $guru->absensi->first()->waktu_absen->setTimezone('Asia/Makassar')->format('H:i') }}
                            </p>
                        </div>
                    </li>
                @empty
                    <li class="py-4 text-center text-gray-500 text-sm">Belum ada.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-red-600 mb-4">Hadir Terlambat ({{ $guruTerlambat->count() }})</h3>
            <ul class="max-h-96 overflow-y-auto divide-y divide-gray-200">
                @forelse ($guruTerlambat as $guru)
                    <li class="py-3 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <span class="font-semibold text-red-700">{{ strtoupper(substr($guru->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $guru->name }}</p>
                                <p class="text-sm text-gray-500">{{ $guru->nidn ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-sm text-red-500">
                                {{ $guru->absensi->first()->waktu_absen->setTimezone('Asia/Makassar')->format('H:i') }}
                            </p>
                        </div>
                    </li>
                @empty
                    <li class="py-4 text-center text-gray-500 text-sm">Belum ada.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-500 mb-4">Tidak Hadir ({{ $guruTidakHadir->count() }})</h3>
            <ul class="max-h-96 overflow-y-auto divide-y divide-gray-200">
                @forelse ($guruTidakHadir as $guru)
                    <li class="py-3 flex items-center">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                <span class="font-semibold text-gray-600">{{ strtoupper(substr($guru->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $guru->name }}</p>
                                <p class="text-sm text-gray-500">{{ $guru->nidn ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="py-4 text-center text-gray-500 text-sm">Semua guru hadir.</li>
                @endforelse
            </ul>
        </div>

    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h3>
        <ul class="divide-y divide-gray-200">
            @forelse ($aktivitasTerbaru as $aktivitas)
                <li class="py-3">
                    <p class="font-medium text-gray-900">
                        {{ $aktivitas->pengguna->name ?? 'Pengguna tidak dikenal' }}
                    </p>
                    <div class="flex justify-between text-sm">
                        <p class="text-gray-500">
                            @if ($aktivitas->tipe == 'masuk')
                                <span class="text-green-600">Absen Masuk</span>
                                @if ($aktivitas->status == 'Telat')
                                    <span class="text-red-600">(Terlambat)</span>
                                @endif
                            @else
                                <span class="text-blue-600">Absen Pulang</span>
                            @endif
                        </p>
                        <p class="text-gray-400">
                            {{ $aktivitas->waktu_absen->setTimezone('Asia/Makassar')->format('H:i') }}
                            <span class="text-xs">({{ $aktivitas->waktu_absen->diffForHumans() }})</span>
                        </p>
                    </div>
                </li>
            @empty
                <li class="py-4 text-center text-gray-500 text-sm">
                    Belum ada aktivitas.
                </li>
            @endforelse
        </ul>
    </div>
    
</div>