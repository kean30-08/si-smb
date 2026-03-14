<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Pengajar;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user sudah login DAN datanya ada di tabel pengajars, tolak!
        if (auth()->check() && Pengajar::where('user_id', auth()->id())->exists()) {
            abort(403,'AKSES DITOLAK! Halaman ini khusus untuk Admin Vihara.');
        }

        return $next($request);
    }
}