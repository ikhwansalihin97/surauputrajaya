<?php

namespace App\Http\Controllers\Jawi;

use App\Http\Controllers\Controller;
use App\Mail\PentadbirSurauSediaAdaMail;
use App\Models\EmailLog;
use App\Models\Masjid;
use App\Models\Role;
use App\Models\Surau;
use App\Models\SurauCommittee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Pendaftaran surau sedia ada oleh Pentadbir JAWI.
 *
 * Hanya maklumat asas (nama, kategori, kariah, no. rujukan JAWI) dan
 * Pentadbir Surau diisi di sini. Pentadbir Surau kemudiannya melengkapkan
 * maklumat surau dan senarai AJK sendiri.
 */
class SurauController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $suraus = Surau::with(['masjid', 'user'])
            ->withCount(['committees', 'pertukaranPentadbir as pertukaran_menunggu_count' => fn ($q) => $q->where('status', 'MENUNGGU')])
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('nama_surau', 'like', "%{$search}%")
                    ->orWhere('no_rujukan_jawi', 'like', "%{$search}%");
            }))
            ->orderBy('nama_surau')
            ->paginate(20)
            ->withQueryString();

        return view('admin.jawi.surau.index', compact('suraus', 'search'));
    }

    public function create()
    {
        $masjids = Masjid::where('is_active', true)->orderBy('nama_masjid')->get();

        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'no_kad_pengenalan']);

        return view('admin.jawi.surau.create', compact('masjids', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_surau'        => ['required', 'string', 'max:255'],
            'kategori_surau'    => ['required', Rule::in(['Surau Jumaat', 'Surau Komuniti'])],
            'masjid_id'         => ['required', 'exists:masjids,id'],
            'no_rujukan_jawi'   => ['required', 'string', 'max:100', 'unique:suraus,no_rujukan_jawi'],

            'pentadbir_jenis'   => ['required', Rule::in(['SEDIA_ADA', 'BAHARU'])],
            'pentadbir_user_id' => ['required_if:pentadbir_jenis,SEDIA_ADA', 'nullable', 'exists:users,id'],

            'pentadbir_nama'    => ['required_if:pentadbir_jenis,BAHARU', 'nullable', 'string', 'max:255'],
            'pentadbir_ic'      => ['required_if:pentadbir_jenis,BAHARU', 'nullable', 'regex:/^[0-9]{12}$/', 'unique:users,no_kad_pengenalan'],
            'pentadbir_email'   => ['required_if:pentadbir_jenis,BAHARU', 'nullable', 'email', 'max:255', 'unique:users,email'],
            'pentadbir_tel'     => ['required_if:pentadbir_jenis,BAHARU', 'nullable', 'regex:/^[0-9]+$/', 'max:30'],
            'pentadbir_jawatan' => ['required', Rule::in(SurauCommittee::JAWATAN)],
        ], [
            'no_rujukan_jawi.unique'        => 'No. rujukan JAWI ini telah digunakan oleh surau lain.',
            'pentadbir_user_id.required_if' => 'Sila pilih pengguna sedia ada sebagai Pentadbir Surau.',
            'pentadbir_nama.required_if'    => 'Nama Pentadbir Surau wajib diisi.',
            'pentadbir_ic.required_if'      => 'No. kad pengenalan Pentadbir Surau wajib diisi.',
            'pentadbir_ic.regex'            => 'No. kad pengenalan mestilah 12 digit nombor.',
            'pentadbir_ic.unique'           => 'No. kad pengenalan ini telah berdaftar. Sila pilih "Pengguna Sedia Ada".',
            'pentadbir_email.required_if'   => 'E-mel Pentadbir Surau wajib diisi.',
            'pentadbir_email.unique'        => 'E-mel ini telah berdaftar. Sila pilih "Pengguna Sedia Ada".',
            'pentadbir_tel.required_if'     => 'No. telefon Pentadbir Surau wajib diisi.',
            'pentadbir_tel.regex'           => 'No. telefon hanya boleh mengandungi nombor.',
            'pentadbir_jawatan.required'    => 'Sila pilih jawatan Pentadbir Surau dalam AJK.',
            'pentadbir_jawatan.in'          => 'Jawatan Pentadbir Surau tidak sah.',
        ]);

        $isNewUser = $data['pentadbir_jenis'] === 'BAHARU';

        // Rekod lama mungkin menyimpan No. K/P dengan tanda '-'
        if ($isNewUser && User::whereRaw("REPLACE(no_kad_pengenalan, '-', '') = ?", [$data['pentadbir_ic']])->exists()) {
            return back()->withInput()->withErrors([
                'pentadbir_ic' => 'No. kad pengenalan ini telah berdaftar. Sila pilih "Pengguna Sedia Ada".',
            ]);
        }

        $activationToken = $isNewUser ? Str::random(64) : null;

        [$surau, $user] = DB::transaction(function () use ($data, $isNewUser, $activationToken) {
            if ($isNewUser) {
                $user = User::create([
                    'name'                => $data['pentadbir_nama'],
                    'no_kad_pengenalan'   => $data['pentadbir_ic'],
                    'email'               => $data['pentadbir_email'],
                    'no_telefon_bimbit'   => $data['pentadbir_tel'],
                    'jawatan'             => $data['pentadbir_jawatan'] ?: 'Pentadbir Surau',
                    'status_perkhidmatan' => 'LAIN_LAIN',
                    'password'            => Hash::make(Str::random(40)),
                    'is_active'           => true,
                    'is_first_login'      => true,
                    'activation_token'    => $activationToken,
                ]);
            } else {
                $user = User::findOrFail($data['pentadbir_user_id']);
            }

            $surauRole = Role::firstOrCreate(['slug' => 'surau'], ['name' => 'Pentadbir Surau']);
            $user->roles()->syncWithoutDetaching([$surauRole->id]);

            $surau = Surau::create([
                'nama_surau'      => $data['nama_surau'],
                'kategori_surau'  => $data['kategori_surau'],
                'masjid_id'       => $data['masjid_id'],
                'no_rujukan_jawi' => $data['no_rujukan_jawi'],
                'user_id'         => $user->id,
                'created_by'      => Auth::id(),
            ]);

            // Pentadbir Surau turut disenaraikan sebagai AJK mengikut jawatan yang dipilih
            $surau->committees()->create($surau->pentadbirSebagaiAjk($data['pentadbir_jawatan']));

            return [$surau, $user];
        });

        activity()
            ->useLog('surau')
            ->causedBy(Auth::user())
            ->performedOn($surau)
            ->log("Surau sedia ada '{$surau->nama_surau}' ({$surau->no_rujukan_jawi}) didaftarkan dengan Pentadbir Surau '{$user->name}'.");

        $this->notifyPentadbir($surau->load('masjid'), $user, $activationToken);

        $mesej = $isNewUser
            ? "Surau '{$surau->nama_surau}' berjaya didaftarkan. Pautan pengaktifan akaun telah dihantar kepada {$user->email}."
            : "Surau '{$surau->nama_surau}' berjaya didaftarkan dan ditambah kepada akaun {$user->name}.";

        return redirect()->route('admin.jawi.surau.index')->with('success', $mesej);
    }

    private function notifyPentadbir(Surau $surau, User $user, ?string $activationToken): void
    {
        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $activationUrl = $activationToken ? url('/activate/' . $activationToken) : null;
        $mail = new PentadbirSurauSediaAdaMail($surau, $user, $activationUrl);
        $subject = $mail->envelope()->subject;

        try {
            Mail::to($user->email)->send($mail);
            $status = 'SENT';
            $error  = null;
        } catch (\Throwable $e) {
            Log::error('Gagal hantar e-mel Pentadbir Surau sedia ada: ' . $e->getMessage());
            $status = 'FAILED';
            $error  = $e->getMessage();
        }

        EmailLog::create([
            'application_id' => null,
            'mail_type'      => $activationToken ? 'PENGAKTIFAN_AKAUN' : 'PENTADBIR_SURAU_SEDIA_ADA',
            'to_email'       => json_encode([$user->email]),
            'subject'        => $subject,
            'status'         => $status,
            'error_message'  => $error,
            'sent_at'        => $status === 'SENT' ? now() : null,
        ]);
    }
}
