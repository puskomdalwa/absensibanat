<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasRole
{
    /**
     * Batasi route berdasarkan nilai kolom role.akses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $allowedRoles = array_map('strtolower', $roles);
        $currentRole = strtolower((string) optional($user->role)->akses);

        if (! in_array($currentRole, $allowedRoles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
