<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->role?->name !== 'admin') { // use id inplace of name
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
