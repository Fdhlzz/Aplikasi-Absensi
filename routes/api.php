<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\PerangkatController;

Route::prefix('v1')->group(function () {
    Route::prefix('perangkat')->group(function () {
        // Heartbeat + Cek Command Enroll
        Route::post('heartbeat', [PerangkatController::class, 'heartbeat']);

        // Kirim Absensi
        Route::post('absensi', [AbsensiController::class, 'store']);
    });
});