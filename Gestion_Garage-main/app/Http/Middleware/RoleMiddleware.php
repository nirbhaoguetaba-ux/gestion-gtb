<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userRole = Auth::user()->role;

        foreach ($roles as $role) {
            if ($userRole === $role) {
                return $next($request);
            }
            if (($role === 'technicien' || $role === 'mecanicien') && ($userRole === 'technicien' || $userRole === 'mecanicien')) {
                return $next($request);
            }
        }

        // If authorized role doesn't match, redirect to their respective dashboard
        if ($userRole === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($userRole === 'technicien' || $userRole === 'mecanicien') {
            return redirect()->route('technicien.dashboard');
        }
        if ($userRole === 'client') {
            return redirect()->route('client.dashboard');
        }

        abort(403);
    }
}
