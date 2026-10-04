<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiAccess
{
    /**
     * Handle an incoming request.
     * Mengizinkan akses untuk:
     * - Admin (role_id = 1): bisa akses semua data
     * - User biasa: hanya bisa akses data miliknya sendiri (berdasarkan parameter {user} di route)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $authUser = Auth::user();

        // Admin dan Superadmin bisa akses semua
        if ($authUser->hasRole('admin', 'superadmin')) {
            return $next($request);
        }

        // Untuk route yang memiliki parameter {user}, cek apakah user mengakses datanya sendiri
        $routeUser = $request->route('user');

        if ($routeUser) {
            // $routeUser bisa berupa model instance (route model binding) atau ID
            $userId = $routeUser instanceof \App\Models\User ? $routeUser->id : $routeUser;

            if ((string)$authUser->id !== (string)$userId) {
                abort(403, 'Akses Ditolak. Anda hanya dapat melihat detail absensi Anda sendiri.');
            }
        }

        return $next($request);
    }
}
