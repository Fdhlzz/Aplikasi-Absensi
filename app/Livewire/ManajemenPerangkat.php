<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Perangkat;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class ManajemenPerangkat extends Component
{
    use WithPagination;

    public $showModal = false;
    public $isEditMode = false;
    public $perangkatId;

    // Form Fields
    public $nama = '';
    public $unique_id = '';
    public $api_key = '';

    protected $rules = [
        'nama' => 'required|string|max:255',
        'unique_id' => 'required|string|max:50|unique:perangkat,unique_id', // Unique ID must be unique
        'api_key' => 'required|string|max:64',
    ];

    // Handle unique validation during edit
    protected function rulesForUpdate()
    {
        return [
            'nama' => 'required|string|max:255',
            'unique_id' => 'required|string|max:50|unique:perangkat,unique_id,' . $this->perangkatId,
            'api_key' => 'required|string|max:64',
        ];
    }

    public function resetForm()
    {
        $this->reset(['nama', 'unique_id', 'api_key', 'perangkatId', 'isEditMode']);
    }

    public function generateCredentials()
    {
        // Auto-generate a random ID and Key for convenience
        $this->unique_id = 'ESP32-' . strtoupper(Str::random(6));
        $this->api_key = Str::random(32);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->generateCredentials(); // Pre-fill with new credentials
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->perangkatId = $id;

        $device = Perangkat::findOrFail($id);
        $this->nama = $device->nama;
        $this->unique_id = $device->unique_id;
        $this->api_key = $device->api_key;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate($this->isEditMode ? $this->rulesForUpdate() : $this->rules);

        $data = [
            'nama' => $this->nama,
            'unique_id' => $this->unique_id,
            'api_key' => $this->api_key,
        ];

        if ($this->isEditMode) {
            Perangkat::find($this->perangkatId)->update($data);
        } else {
            Perangkat::create($data);
        }

        $this->showModal = false;
    }

    public function delete($id)
    {
        Perangkat::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.manajemen-perangkat', [
            'devices' => Perangkat::latest()->paginate(10)
        ]);
    }
}