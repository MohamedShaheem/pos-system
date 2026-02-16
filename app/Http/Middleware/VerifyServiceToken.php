<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyServiceToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Service-Token');

        if (!$token || !hash_equals(config('services.accounting.sync_token'), $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized service request',
            ], 401);
        }

        return $next($request);
    }
}

