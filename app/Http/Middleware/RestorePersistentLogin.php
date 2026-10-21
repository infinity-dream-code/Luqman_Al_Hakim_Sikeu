<?php

namespace App\Http\Middleware;

use App\Support\PersistentLogin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestorePersistentLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->routeIs('logout')) {
            PersistentLogin::rehydrate($request);
        }

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('CDN-Cache-Control', 'no-store');

        return $response;
    }
}
