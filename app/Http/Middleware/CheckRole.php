<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Hadkan akses mengikut peranan aktif (session active_role).
 *
 * Penggunaan: ->middleware('role:jawi') atau ->middleware('role:admin,jawi')
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Semak sama ada pengguna telah log masuk
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $activeRole = session('active_role');

        // Peranan aktif mesti dibenarkan untuk laluan ini DAN masih dimiliki oleh pengguna
        // (cth. peranan yang telah ditarik balik selepas log masuk tidak lagi sah).
        if (!in_array($activeRole, $roles, true) || !auth()->user()->hasRole($activeRole)) {
            abort(403, 'Anda tidak mempunyai kebenaran untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
