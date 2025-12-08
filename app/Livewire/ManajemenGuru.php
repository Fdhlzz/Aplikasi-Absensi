<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengguna;
use App\Models\Jadwal;
use App\Models\SidikJari; // <-- Needed for fingerprint table
use App\Models\Perangkat; // <-- Needed to send command to ESP32
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ManajemenGuru extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $search = '';

    // Modal States
    public $showModal = false;       // Edit User Modal
    public $showEnrollModal = false; // Enroll Fingerprint Modal
    public $isEditMode = false;
    
    public $penggunaId;

    // User Form Fields
    public $name = '';
    public $email = '';
    public $nidn = '';
    public $jadwal_id = '';

    // Enroll Form Fields
    public $fingerprint_id = ''; 
    public $selectedGuruName = '';

    // --- Validation ---
    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => ['required', 'email', Rule::unique('pengguna')->ignore($this->penggunaId)],
            'nidn' => ['nullable', 'string', 'min:5', Rule::unique('pengguna')->ignore($this->penggunaId)],
            'jadwal_id' => 'required|integer|exists:jadwal,id',
        ];
    }
    
    public function updatingSearch() { $this->resetPage(); }
    
    public function resetForm()
    {
        $this->reset(['name', 'email', 'nidn', 'jadwal_id', 'penggunaId', 'isEditMode', 'fingerprint_id', 'selectedGuruName']);
        $this->resetErrorBag();
    }

    // --- User CRUD Logic ---
    
    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->penggunaId = $id;
        $pengguna = Pengguna::findOrFail($id);
        $this->name = $pengguna->name;
        $this->email = $pengguna->email;
        $this->nidn = $pengguna->nidn;
        $this->jadwal_id = $pengguna->jadwal_id;
        $this->showModal = true;
    }

    public function save()
    {
        $validatedData = $this->validate();
        $data = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'nidn' => $validatedData['nidn'],
            'jadwal_id' => $validatedData['jadwal_id'],
            'role' => 'guru',
        ];

        if (!$this->isEditMode) {
            $data['password'] = Hash::make('password'); 
        }

        if ($this->isEditMode) {
            Pengguna::find($this->penggunaId)->update($data);
        } else {
            Pengguna::create($data);
        }
        $this->closeModal();
    }
    
    public function delete($id)
    {
        Pengguna::findOrFail($id)->delete();
    }

    // --- ENROLLMENT LOGIC (This is what you needed) ---

    public function openEnrollModal($id)
    {
        $this->resetForm();
        $this->penggunaId = $id;
        $guru = Pengguna::with('sidikJari')->findOrFail($id);
        $this->selectedGuruName = $guru->name;

        // Check if this guru already has a fingerprint registered
        if ($guru->sidikJari->isNotEmpty()) {
            $this->fingerprint_id = $guru->sidikJari->first()->fingerprint_id;
        } else {
            // Auto-Calculate Next ID: Find max ID in DB + 1
            $maxId = SidikJari::max('fingerprint_id') ?? 0;
            $this->fingerprint_id = $maxId + 1;
        }

        $this->showEnrollModal = true;
    }

    public function saveFingerprint()
    {
        $this->validate([
            'fingerprint_id' => [
                'required', 
                'integer', 
                'min:1', 
                'max:127',
                // ID must be unique in sidik_jari table (unless it belongs to current user)
                Rule::unique('sidik_jari')->ignore($this->penggunaId, 'pengguna_id')
            ]
        ]);

        // 1. Save/Update Link in Database
        SidikJari::updateOrCreate(
            ['pengguna_id' => $this->penggunaId],
            ['fingerprint_id' => $this->fingerprint_id]
        );

        // 2. Send Command to Device (Remote Enrollment)
        // We grab the first available device to execute the command
        $device = Perangkat::first();
        
        if ($device) {
            // Write the ID to 'pending_enrollment_id'
            // The ESP32 will pick this up via heartbeat
            $device->update([
                'pending_enrollment_id' => $this->fingerprint_id
            ]);
            
            session()->flash('message', "Perintah dikirim ke {$device->nama}. Alat akan masuk mode daftar dalam beberapa detik.");
        } else {
            session()->flash('error', 'Gagal: Tidak ada perangkat yang terdaftar di sistem.');
        }

        $this->closeModal();
    }

    public function deleteFingerprint()
    {
        SidikJari::where('pengguna_id', $this->penggunaId)->delete();
        $this->closeModal();
    }

    // --- General ---
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->showEnrollModal = false;
        $this->resetForm();
    }

    public function render()
    {
        $query = Pengguna::where('role', 'guru');

        if (strlen($this->search) > 0) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('nidn', 'like', '%' . $this->search . '%');
            });
        }
        
        $guruList = $query->with(['jadwal', 'sidikJari']) 
                         ->orderBy('name', 'asc')
                         ->paginate(10);
        
        $jadwalListOptions = Jadwal::orderBy('nama')->get();
        
        return view('livewire.manajemen-guru', [
            'guruList' => $guruList,
            'jadwalListOptions' => $jadwalListOptions,
        ]);
    }
}