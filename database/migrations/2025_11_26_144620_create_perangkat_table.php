<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perangkat', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // e.g., "Perangkat Lobi"
            $table->string('unique_id')->unique(); // ESP32 Hardware ID
            $table->string('api_key', 64)->unique();
            $table->timestamp('last_heartbeat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perangkat');
    }
};