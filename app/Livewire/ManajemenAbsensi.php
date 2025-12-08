<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Absensi;
use App\Models\Pengguna;
use Livewire\WithPagination;
use Carbon\Carbon;

class ManajemenAbsensi extends Component
{
    use WithPagination;

    // Filters
    public $dateFilter;
    public $userFilter = '';

    // Modal State
    public $showModal = false;
    public $isEditMode = false;
    public $absensiId;

    // Form Fields
    public $pengguna_id = '';
    public $tanggal_absen = ''; // Date only (Y-m-d)
    public $status = 'Sakit';   // Default status
    public $catatan = '';

    public function mount()
    {
        $this->dateFilter = Carbon::today()->format('Y-m-d');
    }

    public function resetForm()
    {
        $this->reset(['pengguna_id', 'status', 'catatan', 'absensiId', 'isEditMode']);
        $this->tanggal_absen = Carbon::now()->format('Y-m-d');
        $this->status = 'Sakit';
    }

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
        $this->absensiId = $id;

        $absensi = Absensi::findOrFail($id);
        $this->pengguna_id = $absensi->pengguna_id;
        $this->tanggal_absen = $absensi->waktu_absen->format('Y-m-d');
        $this->status = $absensi->status;
        $this->catatan = $absensi->catatan;

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        // 1. Validation
        $rules = [
            'pengguna_id' => 'required|exists:pengguna,id',
            'tanggal_absen' => 'required|date',
            'status' => 'required|in:Sakit,Izin', // Strict restriction
            'catatan' => 'nullable|string|max:255',
        ];

        // If 'Izin', description is mandatory
        if ($this->status == 'Izin') {
            $rules['catatan'] = 'required|string|max:255';
        }

        $this->validate($rules);

        // 2. Prepare Data
        // Set time to 00:00:00 as Sakit/Izin is a daily status
        $waktu = Carbon::parse($this->tanggal_absen)->setTime(0, 0, 0);

        $data = [
            'pengguna_id' => $this->pengguna_id,
            'waktu_absen' => $waktu,
            'tipe' => 'masuk', // Count as 'masuk' so they appear as accounted for
            'status' => $this->status,
            'catatan' => $this->catatan,
            'sumber' => 'manual',
            'diubah_oleh_admin_id' => auth()->id(),
        ];

        if ($this->isEditMode) {
            Absensi::find($this->absensiId)->update($data);
        } else {
            // Check for duplicates on this day
            $exists = Absensi::where('pengguna_id', $this->pengguna_id)
                             ->whereDate('waktu_absen', $this->tanggal_absen)
                             ->exists();
            
            if ($exists) {
                $this->addError('pengguna_id', 'Guru ini sudah memiliki data absensi pada tanggal tersebut.');
                return;
            }
            Absensi::create($data);
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        Absensi::findOrFail($id)->delete();
    }

    public function render()
    {
        $query = Absensi::with('pengguna');

        if ($this->dateFilter) {
            $query->whereDate('waktu_absen', $this->dateFilter);
        }

        if ($this->userFilter) {
            $query->whereHas('pengguna', function($q) {
                $q->where('name', 'like', '%' . $this->userFilter . '%');
            });
        }

        $absensiList = $query->latest('waktu_absen')->paginate(10);
        $guruList = Pengguna::where('role', 'guru')->orderBy('name')->get();

        return view('livewire.manajemen-absensi', [
            'absensiList' => $absensiList,
            'guruList' => $guruList,
        ]);
    }
}