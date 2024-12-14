<?php

namespace App\Http\Middleware;

use Closure;

class DisableApiCache
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        return $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }
}