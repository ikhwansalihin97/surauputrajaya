<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            // 1. Logik Paksaan First Login: Wajib aktifkan 2FA jika kali pertama log masuk
            if ($user->is_first_login && !$user->google2fa_enabled) {
                if (!$request->is('2fa/setup') && !$request->is('2fa/enable') && !$request->is('logout')) {
                    return redirect()->route('2fa.setup')->with('warning', 'Log masuk kali pertama: Anda diwajibkan mengaktifkan 2FA terlebih dahulu.');
                }
            }

            // 2. Logik Sesi 2FA: Jika 2FA sudah diaktifkan tetapi belum melepasi pengesahan 6-digit di sesi ini
            if ($user->google2fa_enabled && !session('2fa_passed')) {
                if (!$request->is('2fa*') && !$request->is('logout')) {
                    return redirect()->route('2fa.index');
                }
            }
        }

        return $next($request);
    }
}