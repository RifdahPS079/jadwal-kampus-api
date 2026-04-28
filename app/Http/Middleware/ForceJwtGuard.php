<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJwtGuard
{
    public function handle(Request $request, Closure $next, string $guard)
    {
        // paksa Laravel pakai guard ini untuk request sekarang
        auth()->shouldUse($guard);

        return $next($request);
    }
}
