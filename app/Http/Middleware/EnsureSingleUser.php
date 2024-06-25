<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Setting;

class EnsureSingleUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if there is already a user registered
        if (Setting::where('key', 'single_user_mode')->first()->value && User::count() >= 1) {
            return redirect('/login')->with('error', 'Single-User Modus is activated. No more registrations possible.');
        }

        return $next($request);
    }
}
