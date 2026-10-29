<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Tetapkan peranan aktif default (peranan pertama pengguna)
            $firstRole = $user->roles()->first();
            session(['active_role' => $firstRole ? $firstRole->slug : 'user']);

            // Spatie Activitylog: Rakam Aktiviti Log Masuk (Guna useLog)
            activity()
                ->useLog('kawalan_keselamatan')
                ->causedBy($user)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'active_role' => session('active_role')
                ])
                ->log("Pengguna '{$user->name}' berjaya log masuk ke dalam sistem.");

            // Jika Pengguna mendayakan 2FA
            if ($user->google2fa_enabled) {
                session(['2fa_passed' => false]);
                return redirect()->route('2fa.index');
            }

            session(['2fa_passed' => true]);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'E-mel atau kata laluan tidak sah.']);
    }

    // 1. Paparkan borang masukan 6-digit kod 2FA
    public function show2faForm()
    {
        return view('auth.2fa_verify');
    }

    // 2. Sahkan kod 6-digit daripada apl Google Authenticator
    public function verify2fa(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|numeric',
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if ($valid) {
            session(['2fa_passed' => true]);

            // Spatie Activitylog: Rakam Pengesahan 2FA
            activity()
                ->useLog('kawalan_keselamatan')
                ->causedBy($user)
                ->log("Pengguna '{$user->name}' berjaya melepasi pengesahan 2FA.");

            return redirect()->route('dashboard');
        }

        return back()->withErrors(['one_time_password' => 'Kod pengesahan 2FA tidak sah. Sila cuba lagi.']);
    }

    // 3. Paparkan skrin Jana QR Code (Setup 2FA)
    public function setup2fa()
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        if (!$user->google2fa_secret) {
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name', 'Sistem Surau'),
            $user->email,
            $user->google2fa_secret
        );

        // Guna BaconQrCode untuk bina QR
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCodeImage = base64_encode($writer->writeString($qrCodeUrl));

        return view('auth.2fa_setup', compact('qrCodeImage', 'user'));
    }

    // 4. Aktifkan 2FA selepas pengesahan pertama
    public function enable2fa(Request $request)
    {
        $user = Auth::user();
        $user->google2fa_enabled = true;
        $user->is_first_login = false; // Matikan status first login
        $user->save();

        session(['2fa_passed' => true]);

        // Spatie Activitylog: Rakam Aktifkan 2FA
        activity()
            ->useLog('kawalan_keselamatan')
            ->causedBy($user)
            ->log("Pengguna '{$user->name}' telah mendayakan Google Authenticator (2FA).");

        return redirect()->route('dashboard')->with('success', 'Google Authenticator (2FA) berjaya diaktifkan!');
    }

    // Fungsi Tukar Peranan (Switch Role)
    public function switchRole(Request $request)
    {
        $request->validate([
            'role_slug' => 'required|string',
        ]);

        $user = Auth::user();

        // Pastikan pengguna sememangnya memiliki role yang dipinta
        if ($user->hasRole($request->role_slug)) {
            session(['active_role' => $request->role_slug]);

            // Spatie Activitylog: Rakam Tukar Peranan (Guna useLog)
            activity()
                ->useLog('tukar_peranan')
                ->causedBy($user)
                ->withProperties([
                    'new_role' => $request->role_slug,
                    'ip_address' => $request->ip()
                ])
                ->log("Pengguna '{$user->name}' menukar peranan aktif kepada: " . strtoupper($request->role_slug));

            return redirect()->route('dashboard')->with('success', 'Peranan berjaya ditukar!');
        }

        return back()->with('error', 'Anda tidak mempunyai hak akses untuk peranan tersebut.');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Spatie Activitylog: Rakam Log Keluar
            activity()
                ->useLog('kawalan_keselamatan')
                ->causedBy($user)
                ->log("Pengguna '{$user->name}' telah log keluar dari sistem.");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal');
    }
}