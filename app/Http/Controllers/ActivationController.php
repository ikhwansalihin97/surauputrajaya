<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ActivationController extends Controller
{
    /**
     * Paparkan borang penetapan kata laluan untuk pengaktifan akaun.
     */
    public function showForm(string $token)
    {
        $user = User::where('activation_token', $token)->first();

        // Token tidak wujud
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Pautan pengaktifan akaun tidak sah atau telah tamat tempoh. Sila hubungi pentadbir sistem.');
        }

        // Akaun sudah pernah diaktifkan (token sudah dibersihkan)
        if (!$user->is_first_login && empty($user->activation_token)) {
            return redirect()->route('login')
                ->with('info', 'Akaun anda telah pun diaktifkan. Sila log masuk menggunakan e-mel dan kata laluan anda.');
        }

        return view('auth.activate', compact('user', 'token'));
    }

    /**
     * Proses penetapan kata laluan dan aktifkan akaun.
     */
    public function setPassword(Request $request, string $token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Pautan pengaktifan akaun tidak sah atau telah tamat tempoh.');
        }

        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'password.required'  => 'Kata laluan baharu wajib diisi.',
            'password.min'       => 'Kata laluan mestilah sekurang-kurangnya 8 aksara.',
            'password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
        ]);

        // Kemaskini kata laluan & bersihkan token pengaktifan
        $user->password         = Hash::make($request->password);
        $user->activation_token = null;
        $user->is_first_login   = false;
        $user->is_active        = true;
        $user->save();

        // Log masuk user secara automatik
        Auth::login($user);

        // Tetapkan sesi peranan & bypass 2FA untuk sesi pengaktifan ini
        $firstRole = $user->roles()->first();
        session([
            'active_role' => $firstRole ? $firstRole->slug : 'surau',
            '2fa_passed'  => true,
        ]);

        // Rakam aktiviti log
        activity()
            ->useLog('kawalan_keselamatan')
            ->causedBy($user)
            ->log("Pengguna '{$user->name}' telah berjaya mengaktifkan akaun dan menetapkan kata laluan baharu.");

        return redirect()->route('dashboard')
            ->with('success', 'Selamat datang! Akaun anda telah berjaya diaktifkan. Sila semak maklumat surau anda di bawah.');
    }
}
