<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;   // ✅ WAJIB INI
use App\Models\MataKuliah;

class DosenKelasController extends Controller
{
   public function mataKuliahSaya()
{
    $dosen = auth('dosen')->user();

    if (!$dosen) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 401);
    }

    $matkul = \App\Models\PengampuMataKuliah::with('mataKuliah')
        ->where('dosen_id', $dosen->id)
        ->get()
        ->pluck('mataKuliah')
        ->filter()
        ->values();

    return response()->json([
        'data' => $matkul
    ]);
}
}