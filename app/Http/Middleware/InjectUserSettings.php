<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InjectUserSettings
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Share settings with all Inertia pages
            Inertia::share([
                'settings' => function () use ($user) {
                    return $user->getSettings();
                },
            ]);
        }

        return $next($request);
    }
}
