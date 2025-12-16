<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockDirectStorageAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        // Agar direct /storage access hai aur /vikas/storage nahi hai → block
        if (preg_match('#^storage/#', $path) && !preg_match('#^vikas/storage/#', $path)) {
            abort(404);
        }

        return $next($request);
    }
}
