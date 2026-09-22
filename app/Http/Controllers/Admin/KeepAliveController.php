<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\PersistentLogin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeepAliveController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = Auth::user() ?: PersistentLogin::userFromRequest($request);
        if ($user) {
            PersistentLogin::bind($user);
            PersistentLogin::queue($user);
        }

        return response()->json([
            'ok' => true,
            'authenticated' => (bool) $user,
            'token' => csrf_token(),
        ])->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('CDN-Cache-Control', 'no-store');
    }
}
