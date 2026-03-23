<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Pengajar;

class AdminOnly
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && Pengajar::where('user_id', auth()->id())->exists()) {
            abort(403,'AKSES DITOLAK! Halaman ini khusus untuk Admin Vihara.');
        }

        return $next($request);
    }
}