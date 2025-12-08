<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Livewire\DasborUtama;
use App\Livewire\ManajemenGuru;
use App\Livewire\ManajemenJadwal;
use App\Livewire\LaporanAbsensi;
use App\Livewire\ManajemenPerangkat;
use App\Livewire\ManajemenAbsensi;

// We will import the Livewire components later when we recreate them
// use App\Livewire\DasborUtama; 
// ...

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. AUTH ROUTES ---
Route::get('login', [AuthController::class, 'create'])->name('login');
Route::post('login', [AuthController::class, 'store']);
Route::post('logout', [AuthController::class, 'destroy'])->name('logout');

// --- 2. REDIRECT ROOT ---
Route::get('/', function () {
    return redirect()->route('login');
});

// --- 3. ADMIN GROUP (Protected) ---
Route::prefix('admin')->middleware(['auth'])->group(function () {
    
    Route::get('/', function () { return redirect()->route('admin.dashboard'); });

    // We will uncomment these as we restore the components in the next phase
    Route::get('dashboard', DasborUtama::class)->name('admin.dashboard');
    Route::get('guru', ManajemenGuru::class)->name('admin.guru');
    Route::get('jadwal', ManajemenJadwal::class)->name('admin.jadwal');
    Route::get('absensi', ManajemenAbsensi::class)->name('admin.absensi');
    Route::get('laporan', LaporanAbsensi::class)->name('admin.laporan'); 
    Route::get('perangkat', ManajemenPerangkat::class)->name('admin.perangkat');
    
    // Temporary placeholder so you don't get a 404 after logging in
    // Route::get('dashboard', function() {
    //     return "Dashboard is coming back soon! <form method='POST' action='/logout'><input type='hidden' name='_token' value='".csrf_token()."'><button type='submit'>Logout</button></form>";
    // })->name('admin.dashboard');

});