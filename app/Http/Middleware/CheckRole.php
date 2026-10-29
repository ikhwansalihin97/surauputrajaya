<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Semak sama ada pengguna telah log masuk
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Semak peranan aktif di dalam sesi (Session)
        $activeRole = session('active_role');

        if ($activeRole !== $role) {
            abort(403, 'Anda tidak mempunyai kebenaran untuk mengakses peranan ini.');
        }

        return $next($request);
    }
}