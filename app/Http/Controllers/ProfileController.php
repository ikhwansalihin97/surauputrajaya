<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    /**
     * Tandatangan pengguna (dilukis atau dimuat naik) dan baris di bawah nama dalam surat.
     * Imej disimpan dalam storan peribadi (bukan awam).
     */
    public function updateTandatangan(Request $request)
    {
        $request->validate([
            'tandatangan_lukisan' => ['nullable', 'string', 'max:2000000'],
            'tandatangan_fail'    => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'blok_tandatangan'    => ['nullable', 'string', 'max:500'],
            'buang_tandatangan'   => ['nullable', 'boolean'],
        ], [
            'tandatangan_fail.image' => 'Fail tandatangan mestilah imej PNG atau JPG.',
            'tandatangan_fail.max'   => 'Saiz fail tandatangan tidak boleh melebihi 2MB.',
        ]);

        $user = Auth::user();
        $baharu = null;

        if ($request->hasFile('tandatangan_fail')) {
            $baharu = $request->file('tandatangan_fail')->store('tandatangan', 'local');
        } elseif ($lukisan = $request->input('tandatangan_lukisan')) {
            $png = preg_match('#^data:image/png;base64,([A-Za-z0-9+/=]+)$#', $lukisan, $m) ? base64_decode($m[1], true) : false;
            $sah = $png && str_starts_with($png, "\x89PNG\r\n\x1a\n") && (@getimagesizefromstring($png)[2] ?? null) === IMAGETYPE_PNG;
            if (!$sah) {
                return back()->withErrors(['tandatangan_lukisan' => 'Lukisan tandatangan tidak sah. Sila cuba lagi.']);
            }
            $baharu = 'tandatangan/' . Str::random(40) . '.png';
            Storage::disk('local')->put($baharu, $png);
        }

        $data = ['blok_tandatangan' => trim((string) $request->input('blok_tandatangan')) ?: null];
        if ($baharu || $request->boolean('buang_tandatangan')) {
            if ($user->tandatangan_path) {
                Storage::disk('local')->delete($user->tandatangan_path);
            }
            $data['tandatangan_path'] = $baharu;
        }
        $user->update($data);

        return redirect()->to(route('profile.edit') . '#tandatangan')
            ->with('tandatangan_success', 'Tandatangan anda telah disimpan.');
    }

    public function imejTandatangan()
    {
        $laluan = Auth::user()->tandatangan_path;
        abort_unless($laluan && Storage::disk('local')->exists($laluan), 404);

        return response()->file(Storage::disk('local')->path($laluan), ['Cache-Control' => 'private, no-store']);
    }
}
