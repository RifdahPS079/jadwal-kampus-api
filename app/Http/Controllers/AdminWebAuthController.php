<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminWebAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $ok = Auth::guard('admin_web')->attempt([
            'username' => $request->username,
            'password' => $request->password,
        ]);

        if (!$ok) {
            return back()
                ->withInput()
                ->withErrors(['login' => 'Username atau password salah']);
        }

        $request->session()->regenerate();

        return redirect()->route('admin.monitoring');
    }

   public function logout()
    {
        auth('admin_web')->logout(); 
        return redirect()->route('admin.login.form');
    }
}
