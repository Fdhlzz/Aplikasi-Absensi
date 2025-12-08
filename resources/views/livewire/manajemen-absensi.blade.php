<div>
    <div class="md:flex md:items-center md:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-semibold text-gray-800">Data Absensi</h1>
        
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <input type="date" wire:model.live="dateFilter" 
                   class="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
            
            <input type="text" wire:model.live.debounce.300ms="userFilter" placeholder="Cari Nama Guru..." 
                   class="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2">

            <button wire:click="openCreateModal" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700 flex items-center justify-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Input Izin / Sakit</span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Guru</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sumber</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($absensiList as $absen)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                            {{ $absen->waktu_absen->format('d M Y') }}
                            @if($absen->sumber == 'perangkat')
                                <div class="text-xs text-gray-400">{{ $absen->waktu_absen->format('H:i') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                            {{ $absen->pengguna->name ?? 'User Terhapus' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $color = match($absen->status) {
                                    'Tepat Waktu' => 'bg-green-100 text-green-800',
                                    'Telat' => 'bg-red-100 text-red-800',
                                    'Pulang Cepat' => 'bg-orange-100 text-orange-800',
                                    'Sakit' => 'bg-purple-100 text-purple-800',
                                    'Izin' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2.5 py-0.5 text-xs rounded-full font-medium {{ $color }}">
                                {{ $absen->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                            {{ $absen->catatan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($absen->sumber == 'manual')
                                <span class="flex items-center gap-1 text-orange-600 font-medium">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    Manual
                                </span>
                            @else
                                <span class="flex items-center gap-1 text-gray-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    Device
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            @if($absen->sumber == 'manual')
                                <button wire:click="openEditModal({{ $absen->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            @endif
                            <button wire:click="delete({{ $absen->id }})" wire:confirm="Hapus data ini?" class="text-red-600 hover:text-red-900">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-gray-500">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-200">{{ $absensiList->links() }}</div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal"></div>
            <div class="relative bg-white w-full max-w-lg mx-auto rounded-lg shadow-xl p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-800">
                    {{ $isEditMode ? 'Edit Izin/Sakit' : 'Input Izin/Sakit Baru' }}
                </h3>
                
                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Guru</label>
                        <select wire:model="pengguna_id" class="w-full border-gray-300 rounded-lg p-2 mt-1 focus:ring-blue-500 focus:border-blue-500" @if($isEditMode) disabled @endif>
                            <option value="">Pilih Guru...</option>
                            @foreach($guruList as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                            @endforeach
                        </select>
                        @error('pengguna_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="date" wire:model="tanggal_absen" class="w-full border-gray-300 rounded-lg p-2 mt-1 focus:ring-blue-500 focus:border-blue-500">
                        @error('tanggal_absen') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="flex gap-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" wire:model.live="status" value="Sakit" class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Sakit</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" wire:model.live="status" value="Izin" class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-gray-700">Izin</span>
                            </label>
                        </div>
                        @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Keterangan 
                            @if($status == 'Izin') <span class="text-red-500">*</span> @else <span class="text-gray-400 text-xs">(Opsional)</span> @endif
                        </label>
                        <textarea wire:model="catatan" rows="3" 
                                  class="w-full border-gray-300 rounded-lg p-2 mt-1 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Contoh: Urusan Keluarga, Rawat Inap, dll."></textarea>
                        @error('catatan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>