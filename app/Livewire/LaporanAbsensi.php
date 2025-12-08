<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pengguna;
use App\Models\Absensi;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanAbsensi extends Component
{
    public $selectedYear;
    public $selectedMonth;
    public $years = [];
    public $laporanData = [];

    public function mount()
    {
        // Generate year range (2024 to Current)
        $this->years = range(Carbon::now()->year, 2024);
        $this->selectedYear = Carbon::now()->year;
        $this->selectedMonth = Carbon::now()->month;
    }

    public function generateReport()
    {
        // 1. Get all Gurus (Exclude Admins)
        $gurus = Pengguna::where('role', 'guru')->orderBy('name')->get();

        $report = [];

        foreach ($gurus as $guru) {
            // 2. Fetch 'masuk' records for this period
            // We count 'masuk' because that represents a "Day" of attendance
            $records = Absensi::where('pengguna_id', $guru->id)
                ->where('tipe', 'masuk')
                ->whereYear('waktu_absen', $this->selectedYear)
                ->whereMonth('waktu_absen', $this->selectedMonth)
                ->get();

            // 3. Calculate Stats
            $sakit = $records->where('status', 'Sakit')->count();
            $izin = $records->where('status', 'Izin')->count();
            
            // "Hadir" means physically present (Exclude Sakit/Izin)
            $hadir = $records->whereNotIn('status', ['Sakit', 'Izin'])->count();
            
            // Breakdown of physical presence
            $telat = $records->where('status', 'Telat')->count();
            $pulangCepat = $records->where('status', 'Pulang Cepat')->count();
            $tepatWaktu = $records->where('status', 'Tepat Waktu')->count();

            $report[] = [
                'name' => $guru->name,
                'nidn' => $guru->nidn,
                'total_hadir' => $hadir,       // Physical presence
                'total_sakit' => $sakit,       // New Column
                'total_izin' => $izin,         // New Column
                'total_telat' => $telat,
                'total_tepat_waktu' => $tepatWaktu,
            ];
        }

        $this->laporanData = $report;
    }

    public function printReport()
    {
        // Ensure data is fresh
        $this->generateReport();

        $monthName = Carbon::create(null, $this->selectedMonth, 1)->translatedFormat('F');
        
        // Generate PDF using the new view with extra columns
        $pdf = Pdf::loadView('reports.absensi-pdf', [
            'laporanData' => $this->laporanData,
            'monthName' => $monthName,
            'year' => $this->selectedYear
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "Laporan_Absensi_{$monthName}_{$this->selectedYear}.pdf");
    }

    public function render()
    {
        if (empty($this->laporanData)) {
            $this->generateReport();
        }

        return view('livewire.laporan-absensi');
    }
}