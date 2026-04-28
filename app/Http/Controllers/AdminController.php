<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function profile(Request $request)
    {
        return response()->json([
            'message' => 'JWT VALID',
            'admin' => $request->user()
        ]);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Logout berhasil']);
    }

}
