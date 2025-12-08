<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perangkat;
use App\Models\SidikJari; // <-- Import SidikJari Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PerangkatController extends Controller
{
    /**
     * Menerima "heartbeat" dari perangkat ESP32.
     * Digunakan untuk update status online DAN mengirim perintah pending (enroll).
     */
    public function heartbeat(Request $request)
    {
        // 1. Validasi input dari ESP32
        $validator = Validator::make($request->all(), [
            'unique_id' => 'required|string|max:255',
            'api_key' => 'required|string|max:64',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Data tidak valid'], 400);
        }

        // 2. Cari perangkat berdasarkan unique_id dan api_key
        $perangkat = Perangkat::where('unique_id', $request->unique_id)
                              ->where('api_key', $request->api_key)
                              ->first();

        // 3. Jika perangkat tidak ditemukan atau API key salah
        if (!$perangkat) {
            return response()->json(['message' => 'Perangkat tidak terautentikasi'], 401);
        }

        // 4. Update timestamp 'last_heartbeat'
        $perangkat->last_heartbeat = now();
        
        // --- LOGIKA CEK PENDING COMMAND ---
        $enrollId = $perangkat->pending_enrollment_id;
        $enrollName = null;
        
        if ($enrollId) {
            // Cari nama user yang memiliki fingerprint_id ini
            $sidikJari = SidikJari::where('fingerprint_id', $enrollId)
                                  ->with('pengguna')
                                  ->first();
            
            if ($sidikJari && $sidikJari->pengguna) {
                $enrollName = $sidikJari->pengguna->name;
            }

            // Hapus command agar tidak dieksekusi berulang kali
            $perangkat->pending_enrollment_id = null;
        }
        
        $perangkat->save();

        // 5. Kirim respon sukses (sertakan enroll_id dan enroll_name jika ada)
        return response()->json([
            'message' => 'Heartbeat diterima',
            'nama_perangkat' => $perangkat->nama,
            'enroll_id' => $enrollId,     // ID Fingerprint (e.g., 5) or null
            'enroll_name' => $enrollName, // Nama User (e.g., "Budi Santoso") or null
        ]);
    }
}