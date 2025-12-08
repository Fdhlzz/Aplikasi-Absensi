<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('cascade');
            $table->timestamp('waktu_absen');
            
            $table->enum('tipe', ['masuk', 'pulang']);
            $table->enum('status', ['Tepat Waktu', 'Telat', 'Sakit', 'Izin']);
            $table->enum('sumber', ['perangkat', 'manual']);
            
            $table->foreignId('perangkat_id')->nullable()->constrained('perangkat')->onDelete('set null');
            
            $table->text('catatan')->nullable();
            $table->foreignId('diubah_oleh_admin_id')->nullable()->constrained('pengguna')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};