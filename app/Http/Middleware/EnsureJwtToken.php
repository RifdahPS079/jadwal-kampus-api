<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class EnsureJwtToken
{
    public function handle(Request $request, Closure $next)
    {
        // Akan throw exception kalau token:
        // - tidak ada
        // - invalid
        // - expired
        JWTAuth::parseToken()->getPayload();

        return $next($request);
    }
}
