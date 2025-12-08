<div>
    <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
        <h1 class="text-2xl font-semibold text-gray-800">Manajemen Jadwal</h1>
        
        <div class="flex items-center w-full md:w-auto space-x-2">
            <div class="relative w-full md:w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </span>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari nama jadwal..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <button type="button" 
                    wire:click="openCreateModal"
                    class="flex-shrink-0 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center space-x-2">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Jadwal</span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" wire:loading.class.delay="opacity-50">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Jadwal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toleransi (Menit)</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($jadwalList as $jadwal)
                    <tr wire:key="jadwal-{{ $jadwal->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $jadwal->nama }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700 font-mono bg-gray-100 inline-block px-2 rounded">
                                {{ \Carbon\Carbon::parse($jadwal->jam_masuk)->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700 font-mono bg-gray-100 inline-block px-2 rounded">
                                {{ \Carbon\Carbon::parse($jadwal->jam_pulang)->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700">{{ $jadwal->toleransi_telat_menit }} Menit</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                            <div class="space-x-2 flex justify-end">
                                <button wire:click="openEditModal({{ $jadwal->id }})" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-md hover:bg-indigo-50">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $jadwal->id }})" wire:confirm="Yakin hapus jadwal ini?" class="text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-50">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12.54 0c-.27-0.041-.54-.082-.811-.124M15 0c0 .08.016.14.026.19m-3.008 0c0 .08.016.14.026.19m0 0c.27.041.54.082.811.124m-1.022-.165L10.5 1.83v2.84c0 .38.31.69.69.69h1.62c.38 0 .69-.31.69-.69V1.83L15 0z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data jadwal.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($jadwalList->hasPages())
            <div class="p-4 border-t border-gray-200">{{ $jadwalList->links() }}</div>
        @endif
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal"></div>
            <div class="relative bg-white w-full max-w-md mx-auto rounded-lg shadow-xl" @click.away="closeModal">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $isEditMode ? 'Edit Jadwal' : 'Tambah Jadwal Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form wire:submit.prevent="save">
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Jadwal</label>
                            <input type="text" wire:model="nama" 
                                   class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Contoh: Shift Pagi">
                            @error('nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div x-data x-init="flatpickr($refs.pickerIn, { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true })">
                                <label class="block text-sm font-medium text-gray-700">Jam Masuk (24H)</label>
                                <input type="text" x-ref="pickerIn" wire:model="jam_masuk" 
                                       class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 bg-white" 
                                       placeholder="08:00">
                                @error('jam_masuk') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div x-data x-init="flatpickr($refs.pickerOut, { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true })">
                                <label class="block text-sm font-medium text-gray-700">Jam Pulang (24H)</label>
                                <input type="text" x-ref="pickerOut" wire:model="jam_pulang" 
                                       class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 bg-white" 
                                       placeholder="16:00">
                                @error('jam_pulang') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Toleransi Telat (Menit)</label>
                            <input type="number" wire:model="toleransi_telat_menit" 
                                   class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Batas toleransi keterlambatan dalam menit.</p>
                            @error('toleransi_telat_menit') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex items-center justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="bg-white text-gray-700 px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700">
                            {{ $isEditMode ? 'Simpan' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>