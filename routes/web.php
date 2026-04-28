<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminWebAuthController;
use App\Http\Controllers\AdminMonitoringController;
use App\Http\Controllers\AdminDosenWebController;
use App\Http\Controllers\AdminMahasiswaWebController;
use App\Http\Controllers\AdminMataKuliahWebController;
use App\Http\Controllers\AdminRuanganWaktuWebController;
use App\Http\Controllers\JadwalController;

Route::prefix('admin')->name('admin.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH ADMIN (WEB)
    |--------------------------------------------------------------------------
    */
    Route::get('/login', [AdminWebAuthController::class, 'showLogin'])->name('login.form');
    Route::post('/login', [AdminWebAuthController::class, 'login'])->name('login.post');

    /*
    |--------------------------------------------------------------------------
    | AREA ADMIN (BUTUH LOGIN SESSION)
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:admin_web')->group(function () {

        // redirect kalau admin buka /admin -> arahkan ke monitoring
        Route::get('/', function () {
            return redirect()->route('admin.monitoring');
        })->name('home');

        Route::post('/logout', [AdminWebAuthController::class, 'logout'])->name('logout');

        // Monitoring jadwal (halaman utama admin)
        Route::get('/monitoring', [AdminMonitoringController::class, 'index'])->name('monitoring');
        Route::post('/monitoring/jadwal/import', [AdminMonitoringController::class, 'importJadwal'])
            ->name('jadwal.import');

        Route::resource('jadwal', JadwalController::class);


        /*
        |--------------------------------------------------------------------------
        | KELOLA DOSEN (WEB)
        |--------------------------------------------------------------------------
        */
        Route::get('/dosen', [AdminDosenWebController::class, 'index'])->name('dosen.index');
        Route::post('/dosen', [AdminDosenWebController::class, 'store'])->name('dosen.store');
        Route::get('/dosen/{dosen}/edit', [AdminDosenWebController::class, 'edit'])->name('dosen.edit');
        Route::put('/dosen/{dosen}', [AdminDosenWebController::class, 'update'])->name('dosen.update');
        Route::delete('/dosen/{dosen}', [AdminDosenWebController::class, 'destroy'])->name('dosen.destroy');
        Route::post('/dosen/import', [AdminDosenWebController::class, 'import'])->name('dosen.import');

         /*
        |--------------------------------------------------------------------------
        | KELOLA MAHASISWA (WEB)
        |--------------------------------------------------------------------------
        */

        Route::get('/mahasiswa', [AdminMahasiswaWebController::class, 'index'])->name('mahasiswa.index');
        Route::post('/mahasiswa', [AdminMahasiswaWebController::class, 'store'])->name('mahasiswa.store');
        Route::post('/mahasiswa/import', [AdminMahasiswaWebController::class, 'import'])->name('mahasiswa.import');
        Route::get('/mahasiswa/{mahasiswa}/edit', [AdminMahasiswaWebController::class, 'edit'])->name('mahasiswa.edit');
        Route::put('/mahasiswa/{mahasiswa}', [AdminMahasiswaWebController::class, 'update'])->name('mahasiswa.update');
        Route::delete('/mahasiswa/{mahasiswa}', [AdminMahasiswaWebController::class, 'destroy'])->name('mahasiswa.destroy'); 

        
         /*
        |--------------------------------------------------------------------------
        | KELOLA MATAKULIAH (WEB)
        |--------------------------------------------------------------------------
        */
        Route::get('/matakuliah', [AdminMataKuliahWebController::class, 'index'])->name('matakuliah.index');
        Route::post('/matakuliah', [AdminMataKuliahWebController::class, 'store'])->name('matakuliah.store');
        Route::post('/matakuliah/import', [AdminMataKuliahWebController::class, 'import'])->name('matakuliah.import');
        Route::get('/matakuliah/{mataKuliah}/edit', [AdminMataKuliahWebController::class, 'edit'])->name('matakuliah.edit');
        Route::put('/matakuliah/{mataKuliah}', [AdminMataKuliahWebController::class, 'update'])->name('matakuliah.update');
        Route::delete('/matakuliah/{mataKuliah}', [AdminMataKuliahWebController::class, 'destroy'])->name('matakuliah.destroy');


        /*
        |--------------------------------------------------------------------------
        | KELOLA RUANGAN/WAKTU (WEB)
        |--------------------------------------------------------------------------
        */
        Route::get('/ruangan-waktu', [AdminRuanganWaktuWebController::class, 'index'])->name('ruangan_waktu.index');

        // RUANGAN
        Route::post('/ruangan-waktu/ruangan', [AdminRuanganWaktuWebController::class, 'storeRuangan'])->name('ruangan.store');
        Route::get('/ruangan-waktu/ruangan/{ruangan}/edit', [AdminRuanganWaktuWebController::class, 'editRuangan'])->name('ruangan.edit');
        Route::put('/ruangan-waktu/ruangan/{ruangan}', [AdminRuanganWaktuWebController::class, 'updateRuangan'])->name('ruangan.update');
        Route::delete('/ruangan-waktu/ruangan/{ruangan}', [AdminRuanganWaktuWebController::class, 'destroyRuangan'])->name('ruangan.destroy');
        Route::post('/ruangan-waktu/ruangan/import', [AdminRuanganWaktuWebController::class, 'importRuangan'])->name('ruangan.import');

        // WAKTU
        Route::post('/ruangan-waktu/waktu', [AdminRuanganWaktuWebController::class, 'storeWaktu'])->name('waktu.store');
        Route::get('/ruangan-waktu/waktu/{waktu}/edit', [AdminRuanganWaktuWebController::class, 'editWaktu'])->name('waktu.edit');
        Route::put('/ruangan-waktu/waktu/{waktu}', [AdminRuanganWaktuWebController::class, 'updateWaktu'])->name('waktu.update');
        Route::delete('/ruangan-waktu/waktu/{waktu}', [AdminRuanganWaktuWebController::class, 'destroyWaktu'])->name('waktu.destroy');
        Route::post('/ruangan-waktu/waktu/import', [AdminRuanganWaktuWebController::class, 'importWaktu'])->name('waktu.import');
    });
});
