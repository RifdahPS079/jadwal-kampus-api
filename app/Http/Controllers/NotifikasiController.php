<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{

public function index(Request $request)
{
    try {

        // 🔥 DETEKSI USER LEBIH AMAN
        $user = auth()->user();
        $role = null;

        if (auth('mahasiswa')->check()) {
            $role = 'mahasiswa';
        } elseif (auth('dosen')->check()) {
            $role = 'dosen';
        }

        if (!$user || !$role) {
            return response()->json([
                'error' => 'User tidak login'
            ], 401);
        }

        $data = Notifikasi::where('user_id', $user->id)
            ->where('role', $role)
            ->latest()
            ->get();

        // mark read
        Notifikasi::where('user_id', $user->id)
            ->where('role', $role)
            ->update(['is_read' => 1]);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}
}
