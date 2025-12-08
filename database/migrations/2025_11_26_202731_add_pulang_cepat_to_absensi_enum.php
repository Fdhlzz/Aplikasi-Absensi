<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'Pulang Cepat' to the allowed list of statuses
        DB::statement("ALTER TABLE absensi MODIFY COLUMN status ENUM('Tepat Waktu', 'Telat', 'Sakit', 'Izin', 'Pulang Cepat') NOT NULL DEFAULT 'Tepat Waktu'");
    }

    public function down(): void
    {
        // Revert back to original list (optional, but good practice)
        DB::statement("ALTER TABLE absensi MODIFY COLUMN status ENUM('Tepat Waktu', 'Telat', 'Sakit', 'Izin') NOT NULL DEFAULT 'Tepat Waktu'");
    }
};