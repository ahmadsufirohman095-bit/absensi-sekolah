<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCacheHeadersAreSet
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Atur header untuk mencegah caching di sisi klien.
        // Ini adalah praktik yang baik untuk aplikasi dinamis untuk memastikan data selalu segar.
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache'); // Kompatibilitas dengan HTTP/1.0
        $response->headers->set('Expires', '0'); // Kompatibilitas dengan proxy

        return $response;
    }
}
