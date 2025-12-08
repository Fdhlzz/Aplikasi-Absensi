<div wire:poll.10s> <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
        <h1 class="text-2xl font-semibold text-gray-800">Perangkat & Alat</h1>
        <button wire:click="openCreateModal" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 flex items-center gap-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Perangkat</span>
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Alat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unique ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">API Key</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($devices as $device)
                    @php
                        // Logic: Online if heartbeat was less than 2 minutes ago
                        $isOnline = $device->last_heartbeat && \Carbon\Carbon::parse($device->last_heartbeat)->diffInMinutes(now()) < 2;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $device->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($isOnline)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5"></span> Online
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <span class="w-2 h-2 bg-gray-400 rounded-full mr-1.5"></span> Offline
                                </span>
                            @endif
                            <div class="text-xs text-gray-400 mt-1">
                                {{ $device->last_heartbeat ? \Carbon\Carbon::parse($device->last_heartbeat)->diffForHumans() : 'Belum pernah aktif' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 select-all">{{ $device->unique_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 select-all">{{ Str::limit($device->api_key, 10) }}...</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="openEditModal({{ $device->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button wire:click="delete({{ $device->id }})" wire:confirm="Hapus perangkat ini?" class="text-red-600 hover:text-red-900">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4 text-gray-500">Belum ada perangkat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="$set('showModal', false)"></div>
            <div class="relative bg-white w-full max-w-md mx-auto rounded-lg shadow-xl p-6">
                <h3 class="text-lg font-bold mb-4">{{ $isEditMode ? 'Edit Perangkat' : 'Tambah Perangkat Baru' }}</h3>
                
                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Alat (Label)</label>
                        <input type="text" wire:model="nama" class="w-full border-gray-300 rounded-lg p-2" placeholder="Contoh: Pintu Depan">
                        @error('nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-xs font-bold text-blue-800 uppercase tracking-wide">Kredensial Alat</h4>
                            <button type="button" wire:click="generateCredentials" class="text-xs text-blue-600 hover:underline">Regenerate</button>
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-xs text-gray-500">Unique ID</label>
                            <input type="text" wire:model="unique_id" class="w-full bg-white border-gray-300 rounded p-1 text-sm font-mono">
                            @error('unique_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-xs text-gray-500">API Key</label>
                            <input type="text" wire:model="api_key" class="w-full bg-white border-gray-300 rounded p-1 text-sm font-mono">
                            @error('api_key') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <p class="text-xs text-blue-600 mt-2">
                            ℹ️ Salin ID dan Key ini ke dalam kode Arduino (ESP32) Anda.
                        </p>
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>