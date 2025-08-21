<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $adminRoleId = 2;

        if (!$user || $user->role_id !== $adminRoleId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
