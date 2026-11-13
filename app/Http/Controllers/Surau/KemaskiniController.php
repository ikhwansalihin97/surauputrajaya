<?php

namespace App\Http\Controllers\Surau;

use App\Http\Controllers\Controller;
use App\Mail\PermohonanBaharuMail;
use App\Models\Application;
use App\Models\ApplicationCommittee;
use App\Models\ApplicationDocument;
use App\Models\ApplicationMeetingMinutes;
use App\Models\EmailLog;
use App\Models\Masjid;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Kemaskini maklumat surau oleh Pentadbir Surau.
 *
 * Rekod yang telah diluluskan TIDAK diubah. Kemaskini disimpan sebagai draf
 * (permohonan KEMASKINI berstatus DRAFT) dan hanya menjadi rekod rasmi selepas
 * dihantar dan diluluskan melalui proses pengesahan Masjid -> JAWI -> Zon.
 */
class KemaskiniController extends Controller
{
    private const DOKUMEN = ['ic_pengerusi', 'ic_setiausaha', 'ic_bendahari', 'pengesahan_kiblat'];

    /**
     * Mula (atau sambung) draf kemaskini daripada rekod rasmi semasa.
     */
    public function start()
    {
        $current = $this->ownedCurrentApplication();

        if (!$current) {
            return redirect()->route('dashboard')
                ->with('kemaskini_error', 'Tiada rekod surau berdaftar (LULUS) di bawah akaun anda untuk dikemaskini.');
        }

        $open = $current->openRevision();

        if ($open && $open->status === 'DRAFT') {
            return redirect()->route('surau.kemaskini.edit', $open->id);
        }

        if ($open) {
            return redirect()->route('dashboard')
                ->with('kemaskini_error', "Permohonan kemaskini {$open->reference_no} sedang dalam proses pengesahan. Kemaskini baharu hanya boleh dibuat selepas ia selesai.");
        }

        $draft = DB::transaction(function () use ($current) {
            $draft = $current->replicate(['reference_no', 'status', 'submitted_at']);
            $draft->reference_no = $this->generateReferenceNo();
            $draft->jenis        = 'KEMASKINI';
            $draft->parent_id    = $current->rootId();
            $draft->status       = 'DRAFT';
            $draft->user_id      = Auth::id();
            $draft->surau_id     = $current->surau_id;

            // Maklumat surau terkini datang daripada rekod rasmi surau (mungkin telah dikemaskini terus)
            if ($current->surau) {
                $draft->fill($current->surau->only(\App\Models\Surau::PROFILE_FIELDS));
            }

            $draft->save();

            // Senarai AJK terkini datang daripada rekod surau (mungkin telah dikemaskini terus)
            $surauAjk = $current->surau?->committees ?? collect();

            if ($surauAjk->isNotEmpty()) {
                $pemohonIc = preg_replace('/\D/', '', (string) $current->pemohon_ic);
                foreach ($surauAjk as $ajk) {
                    ApplicationCommittee::create([
                        'application_id' => $draft->id,
                        'nama'           => $ajk->nama,
                        'ic_no'          => $ajk->ic_no,
                        'jawatan'        => $ajk->jawatan,
                        'alamat'         => $ajk->alamat ?? '',
                        'no_tel'         => $ajk->no_tel ?? '',
                        'email'          => $ajk->email ?? '',
                        'is_applicant'   => $pemohonIc !== '' && preg_replace('/\D/', '', $ajk->ic_no) === $pemohonIc,
                    ]);
                }
            } else {
                foreach ($current->committees as $committee) {
                    $copy = $committee->replicate();
                    $copy->application_id = $draft->id;
                    $copy->save();
                }
            }

            // Dokumen sedia ada dibawa ke draf (fail yang sama, tidak disalin).
            // Senarai AJK dijana semula semasa dihantar; minit mesyuarat mesti baharu.
            foreach ($current->documents->where('jenis_dokumen', '!=', 'SALINAN_SENARAI_AJK') as $document) {
                $copy = $document->replicate();
                $copy->application_id = $draft->id;
                $copy->save();
            }

            return $draft;
        });

        activity()
            ->useLog('permohonan')
            ->causedBy(Auth::user())
            ->performedOn($draft)
            ->log("Draf kemaskini maklumat surau '{$draft->nama_surau}' ({$draft->reference_no}) telah dibuka.");

        return redirect()->route('surau.kemaskini.edit', $draft->id);
    }

    /**
     * Paparkan borang kemaskini (borang permohonan yang sama, diisi dengan maklumat draf).
     */
    public function edit(Request $request, $id)
    {
        $draft = $this->ownedDraft($id);

        // Isi borang dengan data draf melainkan ada input lama (ralat validasi)
        if (!$request->session()->hasOldInput()) {
            $request->session()->now('_old_input', $this->formInputFrom($draft));
        }

        $draft->load(['documents', 'meetingMinutes', 'parent']);

        return view('portal.permohonan', [
            'revision' => $draft,
            'num1'     => null,
            'num2'     => null,
        ]);
    }

    /**
     * Simpan draf, atau hantar untuk pengesahan (action=submit).
     */
    public function update(Request $request, $id)
    {
        $draft    = $this->ownedDraft($id);
        $isSubmit = $request->input('action') === 'submit';

        $request->validate($this->rules($request, $draft, $isSubmit), $this->messages());

        $pemohonIdx = (int) $request->pemohon_index;
        $masjid     = Masjid::findOrFail($request->masjid_id);

        DB::transaction(function () use ($request, $draft, $pemohonIdx, $masjid) {
            $draft->fill([
                'nama_surau'              => $request->nama_surau,
                'kategori_surau'          => $request->kategori_surau,
                'masjid_id'               => $masjid->id,
                'kariah_masjid'           => $masjid->nama_masjid,
                'no_tel_surau'            => $request->no_tel_surau,
                'alamat_surau'            => $request->alamat_surau,
                'alamat_surat'            => $request->alamat_surat,
                'kawasan_surau'           => $request->kawasan_surau,
                'kawasan_lain'            => $request->kawasan_surau === 'Lain-lain' ? $request->kawasan_lain : null,
                'jarak_surau_berdekatan'  => $request->jarak_surau_berdekatan,
                'nama_surau_berdekatan'   => $request->nama_surau_berdekatan,
                'taraf_tapak'             => $request->taraf_tapak,
                'no_lot_geran'            => $request->no_lot_geran,
                'jenis_bangunan'          => $request->jenis_bangunan,
                'tingkat_bangunan'        => $request->tingkat_bangunan,
                'tahun_didirikan'         => $request->tahun_didirikan,
                'jumlah_perbelanjaan'     => $request->jumlah_perbelanjaan,
                'nama_bank'               => $request->nama_bank,
                'no_akaun_bank'           => $request->no_akaun_bank,
                'alamat_bank'             => $request->alamat_bank,
                'penama_akaun'            => $request->penama_akaun,
                'penduduk_lelaki'         => $request->penduduk_lelaki,
                'penduduk_perempuan'      => $request->penduduk_perempuan,
                'kelas_pengajian_sebulan' => $request->kelas_pengajian_sebulan ?? 0,
                'ceramah_sebulan'         => $request->ceramah_sebulan ?? 0,
                'kelas_kanak_sebulan'     => $request->kelas_kanak_sebulan ?? 0,
                'sumber_kewangan'         => array_values(array_filter($request->sumber_kewangan ?? [], 'filled')),
                'pemohon_nama'            => $request->ajk_nama[$pemohonIdx],
                'pemohon_jawatan'         => $request->ajk_jawatan[$pemohonIdx],
                'pemohon_ic'              => $request->ajk_ic[$pemohonIdx],
                'pemohon_email'           => $request->ajk_email[$pemohonIdx],
                'pemohon_tel'             => $request->ajk_tel[$pemohonIdx],
            ]);
            $draft->save();

            $draft->committees()->delete();
            foreach ($request->ajk_nama as $index => $nama) {
                ApplicationCommittee::create([
                    'application_id' => $draft->id,
                    'nama'           => $nama,
                    'ic_no'          => $request->ajk_ic[$index],
                    'jawatan'        => $request->ajk_jawatan[$index],
                    'alamat'         => $request->ajk_alamat[$index],
                    'no_tel'         => $request->ajk_tel[$index],
                    'email'          => $request->ajk_email[$index],
                    'is_applicant'   => ($index === $pemohonIdx),
                ]);
            }

            // Dokumen sokongan: fail baharu menggantikan dokumen jenis yang sama dalam draf
            foreach (self::DOKUMEN as $docKey) {
                if ($request->hasFile($docKey)) {
                    $draft->documents()->where('jenis_dokumen', strtoupper($docKey))->delete();
                    ApplicationDocument::create([
                        'application_id' => $draft->id,
                        'jenis_dokumen'  => strtoupper($docKey),
                        'fail_path'      => $request->file($docKey)->store('dokumen_surau', 'public'),
                        'fail_nama_asal' => $request->file($docKey)->getClientOriginalName(),
                    ]);
                }
            }

            if ($request->hasFile('minit_mesyuarat')) {
                $draft->meetingMinutes()->delete();
                ApplicationMeetingMinutes::create([
                    'application_id'    => $draft->id,
                    'tahun_minit'       => date('Y'),
                    'fail_path'         => $request->file('minit_mesyuarat')->store('minit_mesyuarat', 'public'),
                    'status_pengesahan' => 'DALAM_SEMAKAN',
                ]);
            }
        });

        if (!$isSubmit) {
            return redirect()->route('surau.kemaskini.edit', $draft->id)
                ->with('success', 'Draf kemaskini telah disimpan. Maklumat rasmi surau tidak berubah sehingga kemaskini dihantar dan diluluskan.');
        }

        DB::transaction(function () use ($draft) {
            $this->generateSenaraiAjk($draft);

            $draft->status       = 'PENDING';
            $draft->submitted_at = now();
            $draft->save();
        });

        activity()
            ->useLog('permohonan')
            ->causedBy(Auth::user())
            ->performedOn($draft)
            ->log("Permohonan kemaskini maklumat surau '{$draft->nama_surau}' ({$draft->reference_no}) telah dihantar untuk pengesahan.");

        $this->notifyMasjid($draft);

        return redirect()->route('dashboard')
            ->with('kemaskini_success', "Permohonan kemaskini {$draft->reference_no} telah dihantar untuk pengesahan Pentadbir Masjid & JAWI. Maklumat rasmi surau kekal sehingga kemaskini diluluskan.");
    }

    /**
     * Buang draf kemaskini.
     */
    public function destroy($id)
    {
        $draft = $this->ownedDraft($id);
        $ref   = $draft->reference_no;

        // Fail yang dikongsi dengan rekod rasmi tidak dipadam; hanya rekod draf.
        $draft->delete();

        activity()
            ->useLog('permohonan')
            ->causedBy(Auth::user())
            ->log("Draf kemaskini maklumat surau ({$ref}) telah dibuang.");

        return redirect()->route('dashboard')
            ->with('kemaskini_success', 'Draf kemaskini telah dibuang.');
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Rekod rasmi (LULUS terkini) surau yang ditadbir oleh pengguna semasa.
     */
    private function ownedCurrentApplication(): ?Application
    {
        $approved = Application::where('user_id', Auth::id())
            ->where('status', 'APPROVED')
            ->orderByDesc('id')
            ->get();

        foreach ($approved as $application) {
            $current = $application->currentApproved();
            if ($current && (int) $current->user_id === (int) Auth::id()) {
                return $current->load(['committees', 'documents']);
            }
        }

        return null;
    }

    private function ownedDraft($id): Application
    {
        $draft = Application::withDrafts()
            ->where('jenis', 'KEMASKINI')
            ->where('status', 'DRAFT')
            ->findOrFail($id);

        $current = $draft->currentApproved();

        abort_unless(
            (int) $draft->user_id === (int) Auth::id() && $current && (int) $current->user_id === (int) Auth::id(),
            403,
            'Anda tidak dibenarkan mengemaskini maklumat surau ini.'
        );

        return $draft;
    }

    private function rules(Request $request, Application $draft, bool $isSubmit): array
    {
        $ajkCount   = count($request->input('ajk_nama', []));
        $hasMinutes = $draft->meetingMinutes()->exists();

        return [
            'nama_surau'              => ['required', 'string', 'max:255'],
            'kategori_surau'          => ['required', Rule::in(['Surau Jumaat', 'Surau Komuniti'])],
            'masjid_id'               => ['required', 'exists:masjids,id'],
            'no_tel_surau'            => ['nullable', 'regex:/^[0-9]+$/', 'max:30'],
            'alamat_surau'            => ['required', 'string'],
            'alamat_surat'            => ['required', 'string'],
            'kawasan_surau'           => ['required', 'string', 'max:255'],
            'kawasan_lain'            => ['nullable', 'required_if:kawasan_surau,Lain-lain', 'string', 'max:255'],
            'jarak_surau_berdekatan'  => ['required', 'numeric', 'min:0'],
            'nama_surau_berdekatan'   => ['required', 'string', 'max:255'],
            'taraf_tapak'             => ['required', 'string', 'max:255'],
            'no_lot_geran'            => ['nullable', 'string', 'max:255'],
            'jenis_bangunan'          => ['required', 'string', 'max:255'],
            'tingkat_bangunan'        => ['required', 'string', 'max:255'],
            'tahun_didirikan'         => ['required', 'integer', 'min:1800', 'max:' . date('Y')],
            'jumlah_perbelanjaan'     => ['required', 'numeric', 'min:0'],
            'nama_bank'               => ['required', 'string', 'max:255'],
            'no_akaun_bank'           => ['required', 'regex:/^[0-9]+$/', 'max:30'],
            'alamat_bank'             => ['required', 'string'],
            'penama_akaun'            => ['required', 'string'],
            'sumber_kewangan'         => ['required', 'array', 'min:1'],
            'sumber_kewangan.*'       => ['nullable', 'string', 'max:255'],
            'penduduk_lelaki'         => ['required', 'integer', 'min:0'],
            'penduduk_perempuan'      => ['required', 'integer', 'min:0'],
            'kelas_pengajian_sebulan' => ['nullable', 'integer', 'min:0'],
            'ceramah_sebulan'         => ['nullable', 'integer', 'min:0'],
            'kelas_kanak_sebulan'     => ['nullable', 'integer', 'min:0'],

            'ajk_nama'                => ['required', 'array', 'min:1'],
            'ajk_nama.*'              => ['required', 'string', 'max:255'],
            'ajk_ic'                  => ['required', 'array', 'size:' . $ajkCount],
            'ajk_ic.*'                => ['required', 'regex:/^[0-9]+$/', 'max:12'],
            'ajk_jawatan'             => ['required', 'array', 'size:' . $ajkCount],
            'ajk_jawatan.*'           => ['required', Rule::in(\App\Models\SurauCommittee::JAWATAN)],
            'ajk_alamat'              => ['required', 'array', 'size:' . $ajkCount],
            'ajk_alamat.*'            => ['required', 'string'],
            'ajk_tel'                 => ['required', 'array', 'size:' . $ajkCount],
            'ajk_tel.*'               => ['required', 'regex:/^[0-9]+$/', 'max:30'],
            'ajk_email'               => ['required', 'array', 'size:' . $ajkCount],
            'ajk_email.*'             => ['required', 'email', 'max:255'],
            'pemohon_index'           => ['required', 'integer', 'min:0', 'max:' . max($ajkCount - 1, 0)],

            'minit_mesyuarat'         => [$isSubmit && !$hasMinutes ? 'required' : 'nullable', 'file', 'mimes:pdf', 'max:5120'],
            'pengesahan_kiblat'       => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'ic_pengerusi'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'ic_setiausaha'           => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'ic_bendahari'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'akuan_pengesahan'        => [$isSubmit ? 'accepted' : 'nullable'],
        ];
    }

    private function messages(): array
    {
        return [
            'minit_mesyuarat.required'  => 'Minit mesyuarat terkini wajib dimuat naik sebelum kemaskini dihantar untuk pengesahan.',
            'akuan_pengesahan.accepted' => 'Sila tandakan pengesahan akuan sebelum menghantar kemaskini.',
            'pemohon_index.*'           => 'Sila tandakan seorang Ahli Jawatankuasa sebagai Pemohon Utama (Pentadbir Surau).',
            'kawasan_lain.required_if'  => 'Sila nyatakan butiran tapak bagi kawasan Lain-lain.',
            'no_akaun_bank.regex'       => 'No. akaun bank hanya boleh mengandungi nombor.',
            'no_tel_surau.regex'        => 'No. telefon surau hanya boleh mengandungi nombor.',
            'ajk_ic.*.regex'            => 'No. kad pengenalan AJK hanya boleh mengandungi nombor.',
            'ajk_tel.*.regex'           => 'No. telefon AJK hanya boleh mengandungi nombor.',
        ];
    }

    /**
     * Data borang daripada draf, dalam format input borang permohonan.
     */
    private function formInputFrom(Application $draft): array
    {
        $committees = $draft->committees()->orderBy('id')->get();

        // Rekod lama mungkin menyimpan sumber_kewangan sebagai JSON berganda
        $sumber = $draft->sumber_kewangan;
        if (is_string($sumber)) {
            $sumber = json_decode($sumber, true);
        }
        $sumber = array_values((array) ($sumber ?: ['']));

        $fields = collect($draft->getAttributes())->only([
            'nama_surau', 'kategori_surau', 'masjid_id', 'no_tel_surau', 'alamat_surau', 'alamat_surat',
            'kawasan_surau', 'kawasan_lain', 'jarak_surau_berdekatan', 'nama_surau_berdekatan', 'taraf_tapak',
            'no_lot_geran', 'jenis_bangunan', 'tingkat_bangunan', 'tahun_didirikan', 'jumlah_perbelanjaan',
            'nama_bank', 'no_akaun_bank', 'alamat_bank', 'penama_akaun', 'penduduk_lelaki', 'penduduk_perempuan',
            'kelas_pengajian_sebulan', 'ceramah_sebulan', 'kelas_kanak_sebulan',
        ])->all();

        $applicantIndex = $committees->search(fn ($c) => $c->is_applicant);

        return $fields + [
            'sumber_kewangan' => $sumber,
            'ajk_nama'        => $committees->pluck('nama')->all() ?: [''],
            'ajk_ic'          => $committees->pluck('ic_no')->all(),
            'ajk_jawatan'     => $committees->pluck('jawatan')->all(),
            'ajk_alamat'      => $committees->pluck('alamat')->all(),
            'ajk_tel'         => $committees->pluck('no_tel')->all(),
            'ajk_email'       => $committees->pluck('email')->all(),
            'pemohon_index'   => $applicantIndex === false ? 0 : $applicantIndex,
        ];
    }

    private function generateReferenceNo(): string
    {
        do {
            $ref = 'SURAU-' . strtoupper(Str::random(6));
        } while (Application::withDrafts()->where('reference_no', $ref)->exists());

        return $ref;
    }

    private function generateSenaraiAjk(Application $draft): void
    {
        $draft->load('committees');
        $pdfPath = 'dokumen_surau/senarai_ajk_' . strtolower($draft->reference_no) . '.pdf';

        Storage::disk('public')->put($pdfPath, Pdf::loadView('pdf.senarai_ajk', ['application' => $draft])->output());

        $draft->documents()->where('jenis_dokumen', 'SALINAN_SENARAI_AJK')->delete();
        ApplicationDocument::create([
            'application_id' => $draft->id,
            'jenis_dokumen'  => 'SALINAN_SENARAI_AJK',
            'fail_path'      => $pdfPath,
            'fail_nama_asal' => 'Senarai_AJK_' . $draft->reference_no . '.pdf',
        ]);
    }

    /**
     * Makluman kepada Pentadbir Masjid (TO) dengan CC JAWI & pemohon,
     * sama seperti permohonan baharu.
     */
    private function notifyMasjid(Application $application): void
    {
        $masjidUsers = User::whereHas('roles', fn ($q) => $q->where('slug', 'masjid'))
            ->whereDoesntHave('roles', fn ($q) => $q->where('slug', 'admin'))
            ->whereHas('masjids', fn ($q) => $q->where('masjids.id', $application->masjid_id))
            ->where('is_active', true)
            ->whereNotNull('email')
            ->pluck('email')->unique()->values()->toArray();

        $ccEmails = User::whereHas('roles', fn ($q) => $q->where('slug', 'jawi'))
            ->where('is_active', true)
            ->whereNotNull('email')
            ->pluck('email')->toArray();

        if (filter_var($application->pemohon_email, FILTER_VALIDATE_EMAIL)) {
            $ccEmails[] = $application->pemohon_email;
        }
        $ccEmails = array_values(array_unique(array_filter($ccEmails)));

        $subject = 'Permohonan Kemaskini Maklumat Surau - ' . $application->reference_no;

        try {
            if (empty($masjidUsers)) {
                throw new \Exception('Tiada Pentadbir Masjid aktif ditemui untuk masjid ID: ' . $application->masjid_id);
            }

            $mailable = Mail::to($masjidUsers);
            if (!empty($ccEmails)) {
                $mailable->cc($ccEmails);
            }
            $mailable->send(new PermohonanBaharuMail($application));

            EmailLog::create([
                'application_id' => $application->id,
                'mail_type'      => 'PERMOHONAN_KEMASKINI',
                'to_email'       => json_encode($masjidUsers),
                'cc_email'       => json_encode($ccEmails),
                'bcc_email'      => null,
                'subject'        => $subject,
                'status'         => 'SENT',
                'error_message'  => null,
                'sent_at'        => now(),
            ]);
        } catch (\Exception $e) {
            EmailLog::create([
                'application_id' => $application->id,
                'mail_type'      => 'PERMOHONAN_KEMASKINI',
                'to_email'       => json_encode($masjidUsers),
                'cc_email'       => json_encode($ccEmails),
                'bcc_email'      => null,
                'subject'        => $subject,
                'status'         => 'FAILED',
                'error_message'  => $e->getMessage(),
                'sent_at'        => null,
            ]);

            Log::error('Gagal menghantar e-mel permohonan kemaskini: ' . $e->getMessage(), [
                'application_id' => $application->id,
                'reference_no'   => $application->reference_no,
            ]);
        }
    }
}
