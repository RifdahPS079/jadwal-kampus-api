<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class DetectJwtGuard
{
    public function handle(Request $request, Closure $next)
    {
        // Ambil token dari header Authorization
        $authHeader = $request->header('Authorization', '');
        if (!str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'errors'  => null,
            ], 401);
        }

        try {
            // decode payload dulu (tanpa authenticate) untuk baca claim role
            $payload = JWTAuth::parseToken()->getPayload();
            $role = $payload->get('role'); // dari getJWTCustomClaims()

            // tentukan guard
            $guard = match ($role) {
                'dosen'     => 'dosen',
                'mahasiswa' => 'mahasiswa',
                default     => 'admin', // fallback kalau token admin tidak punya role claim
            };

            auth()->shouldUse($guard);
            JWTAuth::parseToken()->authenticate(); // set user

            // simpan guard biar controller tahu
            $request->attributes->set('auth_guard', $guard);

            return $next($request);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'errors'  => null,
            ], 401);
        }
    }
}
