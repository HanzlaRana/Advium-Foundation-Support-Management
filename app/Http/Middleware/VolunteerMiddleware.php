<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VolunteerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!in_array(auth()->user()->role, ['volunteer', 'admin', 'superadmin'])) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}