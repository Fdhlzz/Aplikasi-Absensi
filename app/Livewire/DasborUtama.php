<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengguna;
use App\Models\Perangkat;
use App\Models\Absensi;
use Carbon\Carbon;

class DasborUtama extends Component
{
    // Stats Properties
    public $totalGuru;
    public $perangkatOnline;
    public $totalPerangkat;
    public $absensiHariIni;

    // Live Status Properties
    public $guruHadirTepatWaktu = [];
    public $guruTerlambat = [];
    public $guruTidakHadir = [];

    // Activity Feed Property
    public $aktivitasTerbaru = [];

    public function render()
    {
        // --- TIMEZONE CONFIG ---
        $tz = 'Asia/Makassar';
        $today = Carbon::now($tz)->format('Y-m-d'); // Force Today's Date in Makassar
        // -----------------------

        // 1. Load Quick Stats
        $this->totalGuru = Pengguna::where('role', 'guru')->count();
        $this->totalPerangkat = Perangkat::count();
        
        $this->perangkatOnline = Perangkat::where('last_heartbeat', '>=', Carbon::now($tz)->subSeconds(15))
                                          ->count();
                                          
        $this->absensiHariIni = Absensi::where('tipe', 'masuk')
                                       ->whereDate('waktu_absen', $today) // Use Makassar Date
                                       ->count();

        // 2. Load Live Status Lists
        // Get all 'masuk' records for TODAY (Makassar Time)
        $absensiHariIniRecords = Absensi::where('tipe', 'masuk')
                                ->whereDate('waktu_absen', $today)
                                ->get();
        
        // Extract IDs
        $hadirIds = $absensiHariIniRecords->pluck('pengguna_id')->toArray();
        $telatIds = $absensiHariIniRecords->where('status', 'Telat')->pluck('pengguna_id')->toArray();
        $tepatWaktuIds = $absensiHariIniRecords->where('status', 'Tepat Waktu')->pluck('pengguna_id')->toArray();

        // List: Tidak Hadir (Everyone NOT in the hadir list)
        $this->guruTidakHadir = Pengguna::where('role', 'guru')
                                   ->whereNotIn('id', $hadirIds)
                                   ->orderBy('name')
                                   ->get();
        
        // List: Hadir Tepat Waktu
        $this->guruHadirTepatWaktu = Pengguna::whereIn('id', $tepatWaktuIds)
                                   ->with(['absensi' => function ($query) use ($today) {
                                       $query->where('tipe', 'masuk')
                                             ->whereDate('waktu_absen', $today)
                                             ->latest('waktu_absen');
                                   }])
                                   ->orderBy('name')
                                   ->get();
        
        // List: Terlambat
        $this->guruTerlambat = Pengguna::whereIn('id', $telatIds)
                                   ->with(['absensi' => function ($query) use ($today) {
                                       $query->where('tipe', 'masuk')
                                             ->whereDate('waktu_absen', $today)
                                             ->latest('waktu_absen');
                                   }])
                                   ->orderBy('name')
                                   ->get();
                                   
        // 3. Load Recent Activity
        $this->aktivitasTerbaru = Absensi::with('pengguna')
                                         ->whereDate('waktu_absen', $today) // Show today's activity only
                                         ->latest('waktu_absen')
                                         ->take(7)
                                         ->get();

        return view('livewire.dasbor-utama');
    }
}