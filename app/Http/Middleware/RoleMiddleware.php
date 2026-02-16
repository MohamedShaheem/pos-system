<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle($request, Closure $next, $roles)
    {
        $user = $request->user();

        // Split roles by | so 'admin|superadmin' becomes ['admin', 'superadmin']
        $rolesArray = explode('|', $roles);

        if (!$user || !in_array($user->role->name, $rolesArray)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}

