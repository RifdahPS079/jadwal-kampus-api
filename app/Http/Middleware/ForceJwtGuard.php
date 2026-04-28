<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJwtGuard
{
   public function handle($request, Closure $next)
{
    // ✅ IZINKAN LOGIN TANPA TOKEN
    if ($request->is('api/dosen/login') ||
        $request->is('api/mahasiswa/login') ||
        $request->is('api/admin/login')) {
        return $next($request);
    }

    // logic lama kamu (JWT check)
    return $next($request);
}
}
