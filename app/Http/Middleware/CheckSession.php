<?php

namespace App\Http\Middleware;

use App\Support\PersistentLogin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            $user = PersistentLogin::userFromRequest($request);
            if ($user) {
                PersistentLogin::bind($user);
                PersistentLogin::queue($user);
            }
        }

        if (!Auth::check()) {
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json(['retry' => true, 'token' => csrf_token()], 401);
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
