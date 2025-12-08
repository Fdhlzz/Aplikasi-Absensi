<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Jadwal;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Carbon\Carbon; // Import Carbon

class ManajemenJadwal extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $search = '';

    // Modal State
    public $showModal = false;
    public $isEditMode = false;
    public $jadwalId;

    // Form Fields
    public $nama = '';
    public $jam_masuk = '08:00'; // Default 24H format
    public $jam_pulang = '16:00'; // Default 24H format
    public $toleransi_telat_menit = 5;

    protected function rules()
    {
        return [
            'nama' => 'required|string|min:3|max:255|unique:jadwal,nama,' . $this->jadwalId,
            'jam_masuk' => 'required', // Relaxed rule, handled by logic
            'jam_pulang' => 'required',
            'toleransi_telat_menit' => 'required|integer|min:0',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function resetForm()
    {
        $this->reset(['nama', 'jam_masuk', 'jam_pulang', 'toleransi_telat_menit', 'jadwalId', 'isEditMode']);
        $this->resetErrorBag();
        // Ensure defaults are strictly 24H strings
        $this->jam_masuk = '08:00';
        $this->jam_pulang = '16:00';
        $this->toleransi_telat_menit = 5;
    }

    // --- Modal Control ---
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
        $this->jadwalId = $id;

        $jadwal = Jadwal::findOrFail($id);
        $this->nama = $jadwal->nama;
        
        // FORCE 24H FORMAT (H:i)
        // This converts "16:00:00" -> "16:00"
        $this->jam_masuk = Carbon::parse($jadwal->jam_masuk)->format('H:i');
        $this->jam_pulang = Carbon::parse($jadwal->jam_pulang)->format('H:i');
        
        $this->toleransi_telat_menit = $jadwal->toleransi_telat_menit;

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }
    
    // --- CRUD Methods ---
    public function save()
    {
        $validatedData = $this->validate();

        $data = [
            'nama' => $validatedData['nama'],
            // Ensure we store it as H:i:s for MySQL
            'jam_masuk' => Carbon::parse($validatedData['jam_masuk'])->format('H:i:s'),
            'jam_pulang' => Carbon::parse($validatedData['jam_pulang'])->format('H:i:s'),
            'toleransi_telat_menit' => $validatedData['toleransi_telat_menit'],
        ];

        if ($this->isEditMode) {
            Jadwal::find($this->jadwalId)->update($data);
        } else {
            Jadwal::create($data);
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        Jadwal::findOrFail($id)->delete();
        $this->dispatch('refreshComponent'); 
    }
    
    // --- Render ---
    public function render()
    {
        $query = Jadwal::query();

        if (strlen($this->search) > 0) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        $jadwalList = $query->orderBy('nama', 'asc')->paginate(10);
        
        return view('livewire.manajemen-jadwal', [
            'jadwalList' => $jadwalList,
        ]);
    }
}