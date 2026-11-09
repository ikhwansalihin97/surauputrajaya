<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Paparkan halaman profil pengguna yang sedang log masuk.
     */
    public function edit()
    {
        $user = Auth::user()->load(['agency', 'department', 'roles']);

        return view('profile.edit', compact('user'));
    }

    /**
     * Kemaskini maklumat peribadi pengguna sendiri.
     * E-mel, No. KP, agensi, jabatan dan peranan hanya boleh diubah oleh pentadbir.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'no_telefon_bimbit'   => ['required', 'string', 'max:30'],
            'no_telefon_pejabat'  => ['nullable', 'string', 'max:30'],
            'sambungan'           => ['nullable', 'string', 'max:20'],
            'jawatan'             => ['required', 'string', 'max:255'],
            'gred'                => ['nullable', 'string', 'max:50'],
            'skim_perkhidmatan'   => ['nullable', 'string', 'max:255'],
            'status_perkhidmatan' => ['required', Rule::in(['TETAP', 'KONTRAK', 'SAMBILAN', 'LAIN_LAIN'])],
        ], [
            'name.required'                => 'Nama penuh wajib diisi.',
            'no_telefon_bimbit.required'   => 'No. telefon bimbit wajib diisi.',
            'jawatan.required'             => 'Jawatan wajib diisi.',
            'status_perkhidmatan.required' => 'Status perkhidmatan wajib dipilih.',
        ]);

        // Perubahan direkod secara automatik oleh LogsActivity pada model User
        Auth::user()->update($validated);

        return redirect()->route('profile.edit')
            ->with('success', 'Maklumat profil anda telah berjaya dikemaskini.');
    }

    /**
     * Tukar kata laluan pengguna sendiri.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required'         => 'Kata laluan semasa wajib diisi.',
            'current_password.current_password' => 'Kata laluan semasa tidak tepat.',
            'password.required'                 => 'Kata laluan baharu wajib diisi.',
            'password.min'                      => 'Kata laluan mestilah sekurang-kurangnya 8 aksara.',
            'password.confirmed'                => 'Pengesahan kata laluan tidak sepadan.',
            'password.different'                => 'Kata laluan baharu mestilah berbeza daripada kata laluan semasa.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        activity()
            ->useLog('kawalan_keselamatan')
            ->causedBy($user)
            ->log("Pengguna '{$user->name}' telah menukar kata laluan.");

        return redirect()->route('profile.edit')
            ->with('password_success', 'Kata laluan anda telah berjaya ditukar.');
    }
}
