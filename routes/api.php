<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\WaktuController;
use App\Http\Controllers\PengampuMataKuliahController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\NotifikasiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// =======================
// PUBLIC (NO AUTH)
// =======================
Route::get('/test', fn () => response()->json(['status' => 'API OK']));

// login

Route::post('/dosen/login', [AuthController::class, 'loginDosen']);
Route::post('/mahasiswa/login', [AuthController::class, 'loginMahasiswa']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);


// =======================
// ADMIN AREA (WEB) -> prefix /api/admin/...
// Semua CRUD master data ada di sini supaya TIDAK BENTROK dengan /api/dosen/... & /api/mahasiswa/...
// =======================
Route::prefix('admin')
    ->middleware(['jwt.guard:admin', 'auth:admin'])
    ->group(function () {

        Route::post('/logout', [AuthController::class, 'logoutAdmin']);
        Route::get('/profile', [AdminController::class, 'profile']);

        // CRUD master data (admin)
        Route::apiResource('dosen', DosenController::class);
        Route::apiResource('mahasiswa', MahasiswaController::class);
        Route::apiResource('mata-kuliah', MataKuliahController::class);
        Route::apiResource('ruangan', RuanganController::class);
        Route::apiResource('waktu', WaktuController::class);
        Route::apiResource('pengampu', PengampuMataKuliahController::class);
        Route::apiResource('jadwal', JadwalController::class);

        // Import (kalau kamu punya method import)
        Route::post('mata-kuliah/import', [MataKuliahController::class, 'import']);

            // ✅ EDIT jadwal
        Route::put('/jadwal/{id}', [JadwalController::class, 'update']);

        // ✅ HAPUS jadwal
        Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy']);
        });


// =======================
// DOSEN AREA (MOBILE) -> prefix /api/dosen/...
// =======================
Route::prefix('dosen')

    ->group(function () {

        Route::get(
            '/matakuliah-saya',
            [\App\Http\Controllers\Api\DosenKelasController::class, 'mataKuliahSaya']
        );

        Route::post('/logout', [AuthController::class, 'logoutDosen']);

        Route::get(
        '/jadwal/{mataKuliahId}',
        [JadwalController::class, 'jadwalDosenByMataKuliah']
        );

       Route::post(
            '/jadwal/{id}/batalkan',
            [JadwalController::class, 'batalkan']
        );
       
       Route::get('/monitoring', [JadwalController::class, 'monitoringDosen']);

       Route::get(
            '/monitoring',
            [JadwalController::class, 'monitoringDosen']
        );

                Route::post(
            '/jadwal/{jadwal}/ganti',
            [JadwalController::class, 'gantiJadwal']
        );
    });



// =======================
// MAHASISWA AREA (MOBILE) -> prefix /api/mahasiswa/...
// =======================
Route::prefix('mahasiswa')
    ->middleware(['jwt.guard:mahasiswa', 'auth:mahasiswa'])
    ->group(function () {

        Route::get('/profile', fn () => auth('mahasiswa')->user());
        Route::get('/jadwal', [JadwalController::class, 'jadwalMahasiswa']);

        Route::post('/logout', [AuthController::class, 'logoutMahasiswa']);
    });

    Route::middleware('auth:mahasiswa')->get('/mahasiswa/dashboard', [MahasiswaController::class, 'dashboard']);

    Route::middleware(['jwt.token'])->group(function () {
        Route::get('/mobile/me', [MobileController::class, 'me']);
        Route::get('/mobile/jadwal/today', [MobileController::class, 'jadwalToday']); // ?hari=Senin
    });

    Route::middleware('jwt.detect')->group(function () {
        Route::get('/mobile/me', [MobileController::class, 'me']);
        Route::get('/mobile/jadwal/today', [MobileController::class, 'jadwalToday']);
    });

    Route::middleware('auth:mahasiswa')->group(function () {
        Route::get('/mahasiswa/jadwal-saya', [MahasiswaController::class, 'jadwalSaya']);
    });

    // Route::middleware('auth:mahasiswa')->get('/mahasiswa/monitoring', [MahasiswaController::class, 'monitoring']);
    Route::get('/mahasiswa/monitoring', [JadwalController::class, 'monitoringMahasiswa']);
    Route::post('/lupa-katasandi', [AuthController::class, 'lupaKatasandi']);
    Route::post('/verifikasi-akun', [AuthController::class, 'verifikasiAkun']);
    Route::post('/reset-password-manual', [AuthController::class, 'resetPasswordManual']);

    Route::middleware('auth:mahasiswa')->get(
        '/mahasiswa/monitoring',
        [JadwalController::class, 'monitoringMahasiswa']
    );

    
 Route::middleware(['jwt.detect'])->get('/notifikasi', [NotifikasiController::class, 'index']);

 Route::post('/save-fcm-token', [AuthController::class, 'saveFcmToken'])
    ->middleware('auth:dosen,mahasiswa');

Route::get('/fix-pass', function () {
    try {
        $user = \App\Models\Dosen::where('email','putriayu@gmail.com')->first();

        if (!$user) {
            return response()->json(['msg' => 'USER TIDAK ADA']);
        }

        $user->password = bcrypt('123456');
        $user->save();

        return response()->json(['msg' => 'PASSWORD DI RESET']);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ]);
    }
});