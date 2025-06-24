<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class SessionAuth
{
    public function handle($request, Closure $next)
    {
        // Debug log (opsional)
        // \Log::info('Session:', Session::all());

        if (!Session::has('token')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
