<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Perangkat;
use App\Models\SidikJari;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'fingerprint_id' => 'required|integer',
            'device_unique_id' => 'required|string',
            'device_api_key' => 'required|string',
        ]);

        if ($validator->fails()) return response()->json(['message' => 'Data invalid'], 400);

        // 2. Autentikasi Perangkat
        $perangkat = Perangkat::where('unique_id', $request->device_unique_id)
                              ->where('api_key', $request->device_api_key)
                              ->first();

        if (!$perangkat) return response()->json(['message' => 'Unauthorized'], 401);
        
        $perangkat->update(['last_heartbeat' => now()]);

        // 3. Cari User
        $sidikJari = SidikJari::where('fingerprint_id', $request->fingerprint_id)
                              ->with('pengguna.jadwal')
                              ->first();

        if (!$sidikJari) return response()->json(['message' => 'User not found'], 404);

        $pengguna = $sidikJari->pengguna;
        
        // --- TIMEZONE SETUP ---
        $tz = 'Asia/Makassar'; 
        $waktuAbsen = Carbon::now($tz); // Current Time (e.g., 04:02:00)
        // ----------------------

        // 4. Tentukan Masuk/Pulang
        $absensiTerakhir = Absensi::where('pengguna_id', $pengguna->id)
                                    ->whereDate('waktu_absen', $waktuAbsen->format('Y-m-d'))
                                    ->latest('waktu_absen')
                                    ->first();
        
        $tipeAbsen = 'masuk';
        
        if ($absensiTerakhir) {

            if ($absensiTerakhir->tipe == 'masuk') {

                $tipeAbsen = 'pulang';
                // if ($absensiTerakhir->waktu_absen->diffInSeconds($waktuAbsen) < 5) {
                //      return response()->json(['message' => 'Duplicate scan ignored'], 429);
                // }

            } elseif ($absensiTerakhir->tipe == 'pulang') {
                return response()->json(['message' => 'Already completed today'], 409);
            }

        }

        // 5. Hitung Status (Tepat Waktu / Telat / Pulang Cepat)
        $statusAbsen = 'Tepat Waktu';

        if ($pengguna->jadwal) {
            // Parse Schedule Strings
            $timePartsMasuk = explode(':', $pengguna->jadwal->jam_masuk);
            $timePartsPulang = explode(':', $pengguna->jadwal->jam_pulang);

            // Construct Schedule Objects (With Correct Date & Timezone)
            $jamMasuk = Carbon::create($waktuAbsen->year, $waktuAbsen->month, $waktuAbsen->day, (int)$timePartsMasuk[0], (int)$timePartsMasuk[1], (int)$timePartsMasuk[2], $tz);
            $jamPulang = Carbon::create($waktuAbsen->year, $waktuAbsen->month, $waktuAbsen->day, (int)$timePartsPulang[0], (int)$timePartsPulang[1], (int)$timePartsPulang[2], $tz);

            // --- LOGIC MASUK ---
            if ($tipeAbsen == 'masuk') {
                $batasTelat = $jamMasuk->copy()->addMinutes($pengguna->jadwal->toleransi_telat_menit);

                if ($waktuAbsen->gt($batasTelat)) {
                    $statusAbsen = 'Telat';
                }
            } 
            
            // --- LOGIC PULANG ---
            elseif ($tipeAbsen == 'pulang') {
                // If current time is BEFORE the scheduled return time
                if ($waktuAbsen->lt($jamPulang)) {
                    $statusAbsen = 'Pulang Cepat'; // <--- NEW STATUS
                }
            }
        }
        
        // 6. Simpan ke Database
        Absensi::create([
            'pengguna_id' => $pengguna->id,
            'waktu_absen' => $waktuAbsen,
            'tipe' => $tipeAbsen,
            'status' => $statusAbsen,
            'sumber' => 'perangkat',
            'perangkat_id' => $perangkat->id,
        ]);

        // Format for LCD display
        // Example: "Pulang - Pulang Cepat" or "Pulang - Tepat Waktu"
        $displayStatus = ucfirst($tipeAbsen) . " - " . $statusAbsen;

        return response()->json([
            'message' => 'Success',
            'nama' => $pengguna->name,
            'status' => $displayStatus, // This goes to the LCD
            'waktu' => $waktuAbsen->format('H:i'),
        ], 201);
    }
}