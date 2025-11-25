<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // cek apakah user sudah login dan rolenya admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request); // lanjut ke halaman yang dituju
        }

        // kalau bukan admin, redirect ke halaman login atau error
        return redirect('/login')->with('error', 'Akses ditolak, khusus admin.');
    }
}