<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;


class AuthController extends Controller
{
    // =======================
    // ADMIN LOGIN
    // =======================
    public function adminLogin(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        // cari admin
        $admin = Admin::where('username', $data['username'])->first();

        if (!$admin || !Hash::check($data['password'], $admin->password)) {
            return response()->json(['message' => 'Username atau password salah'], 401);
        }

        // INI PENTING: buat token pakai guard admin
        $token = auth('admin')->login($admin);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    public function logoutAdmin()
    {
        auth('admin')->logout();
        return response()->json(['message' => 'Logout admin berhasil']);
    }

    // =======================
    // DOSEN LOGIN
    // =======================


public function loginDosen(Request $request)
{
    $user = \App\Models\Dosen::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'debug' => 'user tidak ditemukan'
        ], 401);
    }

    if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        return response()->json([
            'debug' => 'password tidak cocok',
            'input_password' => $request->password,
            'db_password' => $user->password
        ], 401);
    }

    $token = auth('dosen')->login($user);

    return response()->json([
        'success' => true,
        'token' => $token,
        'user' => $user
    ]);
}

    public function logoutDosen()
    {
        auth('dosen')->logout();
        return response()->json(['message' => 'Logout dosen berhasil']);
    }

    // =======================
    // MAHASISWA LOGIN
    // =======================
   public function loginMahasiswa(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = \App\Models\Mahasiswa::where('email', $request->email)->first();

    if (!$user || !\Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah'
        ], 401);
    }

    $token = auth('mahasiswa')->login($user);

    return response()->json([
        'success' => true,
        'token' => $token,
        'user' => $user
    ]);
}

    public function logoutMahasiswa()
    {
        auth('mahasiswa')->logout();
        return response()->json(['message' => 'Logout mahasiswa berhasil']);
    }

   public function lupaKatasandi(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:dosen,mahasiswa',
            'identitas' => 'required' // NIM / NIDN
        ]);

        if ($request->role === 'dosen') {
            $user = Dosen::where('email', $request->email)
                ->where('nidn', $request->identitas)
                ->first();
        } else {
            $user = Mahasiswa::where('email', $request->email)
                ->where('nim', $request->identitas)
                ->first();
        }

        if (!$user) {
            return response()->json([
                'message' => 'Data tidak cocok'
            ], 404);
        }

        // langsung reset password
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah'
        ]);
    }

    public function verifikasiAkun(Request $request)
{
    $request->validate([
        'role' => 'required',
        'email' => 'required|email',
        'identitas' => 'required'
    ]);

    if ($request->role === 'dosen') {
        $user = Dosen::where('email', $request->email)
            ->where('nidn', $request->identitas)
            ->first();
    } else {
        $user = Mahasiswa::where('email', $request->email)
            ->where('nim', $request->identitas)
            ->first();
    }

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Email atau NIM/NIDN tidak terverifikasi'
        ], 422); // 🔥 penting!
    }

    return response()->json([
        'success' => true,
        'message' => 'Data valid'
    ]);
}

    public function resetPasswordManual(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:dosen,mahasiswa',
            'identitas' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($request->role === 'dosen') {
            $user = \App\Models\Dosen::where('email', $request->email)
                ->where('nidn', $request->identitas)
                ->first();
        } else {
            $user = \App\Models\Mahasiswa::where('email', $request->email)
                ->where('nim', $request->identitas)
                ->first();
        }

        if (!$user) {
            return response()->json(['message' => 'Data tidak valid'], 404);
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah'
        ]);
    }

    public function saveFcmToken(Request $r)
{
    $user = auth()->user(); // user login (dosen / mahasiswa)
    $user->fcm_token = $r->fcm_token;
    $user->save();

    return response()->json(['success' => true]);
}
}
