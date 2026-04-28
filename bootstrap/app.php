<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ForceJwtGuard;
use App\Http\Middleware\DetectJwtGuard;

use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'jwt.guard'  => ForceJwtGuard::class,
            'jwt.detect' => DetectJwtGuard::class, // ✅ tambahan untuk /api/mobile/me
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Helper: hanya format JSON untuk API
        $isApi = fn(Request $request) =>
            $request->is('api/*') || $request->expectsJson();

        // ✅ VALIDATION ERROR (422)
        $exceptions->render(function (ValidationException $e, Request $request) use ($isApi) {
            if (!$isApi($request)) return null;

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);
        });

        // ✅ UNAUTHENTICATED (401) - default Laravel
        $exceptions->render(function (AuthenticationException $e, Request $request) use ($isApi) {
            if (!$isApi($request)) return null;

            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'errors'  => null,
            ], 401);
        });

        // ✅ JWT invalid/expired (401)
        $exceptions->render(function (TokenExpiredException|TokenInvalidException|JWTException $e, Request $request) use ($isApi) {
            if (!$isApi($request)) return null;

            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'errors'  => null,
            ], 401);
        });

        // ✅ UnauthorizedHttpException kadang keluar dari middleware JWT
        $exceptions->render(function (UnauthorizedHttpException $e, Request $request) use ($isApi) {
            if (!$isApi($request)) return null;

            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'errors'  => null,
            ], 401);
        });

        // ✅ 404 / 405 biar Flutter enak
        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($isApi) {
            if (!$isApi($request)) return null;

            return response()->json([
                'success' => false,
                'message' => 'Endpoint tidak ditemukan',
                'errors'  => null,
            ], 404);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) use ($isApi) {
            if (!$isApi($request)) return null;

            return response()->json([
                'success' => false,
                'message' => 'Method tidak diizinkan',
                'errors'  => null,
            ], 405);
        });
    })
    ->create();
