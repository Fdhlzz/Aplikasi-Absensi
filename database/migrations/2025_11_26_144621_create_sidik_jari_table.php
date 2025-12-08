<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sidik_jari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('cascade');
            $table->integer('fingerprint_id')->unique(); // ID stored in AS608 sensor
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sidik_jari');
    }
};