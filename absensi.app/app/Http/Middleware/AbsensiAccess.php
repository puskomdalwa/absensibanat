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

        // Admin bisa akses semua
        if ($authUser->role_id == 1) {
            return $next($request);
        }

        // Untuk route yang memiliki parameter {user}, cek apakah user mengakses datanya sendiri
        $routeUser = $request->route('user');

        if ($routeUser) {
            // $routeUser bisa berupa model instance (route model binding) atau ID
            $userId = $routeUser instanceof \App\Models\User ? $routeUser->id : $routeUser;

            if ($authUser->id != $userId) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }
        }

        return $next($request);
    }
}
