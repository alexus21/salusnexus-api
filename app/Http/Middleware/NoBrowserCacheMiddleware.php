<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoBrowserCacheMiddleware {
    public function handle(Request $request, Closure $next): Response {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate', false);
        $response->headers->set('Pragma', 'no-cache', false);
        $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT', false);

        return $response;
    }
}
