<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        // Check if user has an administrative role slug
        $hasAccess = $user->roles()
            ->whereIn('slug', ['admin', 'super_admin', 'manager'])
            ->exists();

        if (!$hasAccess) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->withErrors([
                'email' => 'Unauthorized area. Administrator credentials required.',
            ]);
        }

        return $next($request);
    }
}