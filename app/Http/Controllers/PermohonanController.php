<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\ApplicationMeetingMinutes;
use App\Models\ApplicationCommittee;
use App\Models\User;
use App\Models\Role;
use App\Models\EmailLog;
use App\Mail\PermohonanBaharuMail;
use App\Mail\MinitMesyuaratDihantarSemulaMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PermohonanController extends Controller
{
    // Paparkan Borang Accordion & Jana Captcha Local
    public function showForm()
    {
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        session(['captcha_answer' => $num1 + $num2]);

        return view('portal.permohonan', compact('num1', 'num2'));
    }

    // Simpan Permohonan Baru (Oleh Guest)
    public function store(Request $request)
    {
        $request->validate([
            'nama_surau'        => 'required|string',
            'kategori_surau'    => 'required|string',
            'masjid_id'         => 'required|exists:masjids,id',
            'nama_bank'         => 'required|string',
            'no_akaun_bank'     => 'required|regex:/^[0-9]+$/|max:30',
            'no_tel_surau'      => 'nullable|regex:/^[0-9]+$/|max:30',
            'ajk_ic'            => 'required|array|min:1',
            'ajk_ic.*'          => 'required|regex:/^[0-9]+$/|max:12',
            'ajk_tel'           => 'required|array|min:1',
            'ajk_tel.*'         => 'required|regex:/^[0-9]+$/|max:30',
            'pemohon_index'     => 'required|numeric',
            'minit_mesyuarat'   => 'required|file|mimes:pdf|max:5120',
            'ic_pengerusi'      => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'ic_setiausaha'     => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'ic_bendahari'      => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pengesahan_kiblat' => 'required|file|mimes:pdf|max:5120',
            'akuan_pengesahan'  => 'accepted',
        ], [
            'no_akaun_bank.regex' => 'No. akaun bank hanya boleh mengandungi nombor.',
            'no_tel_surau.regex'  => 'No. telefon surau hanya boleh mengandungi nombor.',
            'ajk_ic.*.regex'      => 'No. kad pengenalan AJK hanya boleh mengandungi nombor.',
            'ajk_tel.*.regex'     => 'No. telefon AJK hanya boleh mengandungi nombor.',
        ]);

        // 1. Isytiharkan & Ekstrak Data Pemohon Utama SEBELUM DB::transaction
        $pemohonIdx     = (int) $request->pemohon_index;
        $pemohonNama    = $request->ajk_nama[$pemohonIdx] ?? null;
        $pemohonJawatan = $request->ajk_jawatan[$pemohonIdx] ?? null;
        $pemohonIc      = $request->ajk_ic[$pemohonIdx] ?? null;
        $pemohonEmail   = $request->ajk_email[$pemohonIdx] ?? null;
        $pemohonTel     = $request->ajk_tel[$pemohonIdx] ?? null;
        // Dapatkan Masjid berdasarkan ID yang dipilih
        $masjid = \App\Models\Masjid::findOrFail($request->masjid_id);

        // 2. Jalankan Transaksi Pangkalan Data
        $createdData = DB::transaction(function () use ($request, $pemohonIdx, $pemohonNama, $pemohonJawatan, $pemohonIc, $pemohonEmail, $pemohonTel, $masjid) {
            $generatedRefNo = 'SURAU-' . strtoupper(Str::random(6));

            // Simpan Permohonan Utama
            $app = Application::create([
                'reference_no'           => $generatedRefNo,
                'nama_surau'             => $request->nama_surau,
                'kategori_surau'         => $request->kategori_surau,
                'masjid_id'              => $masjid->id,
                'kariah_masjid'          => $masjid->nama_masjid,
                // 'kariah_masjid'          => $request->kariah_masjid,
                'no_tel_surau'           => $request->no_tel_surau,
                'alamat_surau'           => $request->alamat_surau,
                'alamat_surat'           => $request->alamat_surat,
                'kawasan_surau'          => $request->kawasan_surau,
                'kawasan_lain'           => $request->kawasan_lain,
                'jarak_surau_berdekatan' => $request->jarak_surau_berdekatan,
                'nama_surau_berdekatan'  => $request->nama_surau_berdekatan,
                'taraf_tapak'            => $request->taraf_tapak,
                'no_lot_geran'           => $request->no_lot_geran,
                'jenis_bangunan'         => $request->jenis_bangunan,
                'tingkat_bangunan'       => $request->tingkat_bangunan,
                'tahun_didirikan'        => $request->tahun_didirikan,
                'jumlah_perbelanjaan'    => $request->jumlah_perbelanjaan,
                'nama_bank'              => $request->nama_bank,
                'no_akaun_bank'          => $request->no_akaun_bank,
                'alamat_bank'            => $request->alamat_bank,
                'penama_akaun'           => $request->penama_akaun,
                'penduduk_lelaki'        => $request->penduduk_lelaki,
                'penduduk_perempuan'     => $request->penduduk_perempuan,
                'kelas_pengajian_sebulan'=> $request->kelas_pengajian_sebulan ?? 0,
                'ceramah_sebulan'        => $request->ceramah_sebulan ?? 0,
                'kelas_kanak_sebulan'    => $request->kelas_kanak_sebulan ?? 0,
                'sumber_kewangan'        => json_encode($request->sumber_kewangan ?? []),
                'pemohon_nama'           => $pemohonNama,
                'pemohon_jawatan'        => $pemohonJawatan,
                'pemohon_ic'             => $pemohonIc,
                'pemohon_email'          => $pemohonEmail,
                'pemohon_tel'            => $pemohonTel,
                'status'                 => 'PENDING',
            ]);

            // Simpan Senarai AJK Dinamik
            if ($request->has('ajk_nama')) {
                foreach ($request->ajk_nama as $index => $nama) {
                    ApplicationCommittee::create([
                        'application_id' => $app->id,
                        'nama'           => $nama,
                        'ic_no'          => $request->ajk_ic[$index],
                        'jawatan'        => $request->ajk_jawatan[$index],
                        'alamat'         => $request->ajk_alamat[$index],
                        'no_tel'         => $request->ajk_tel[$index],
                        'email'          => $request->ajk_email[$index],
                        'is_applicant'   => ($index === $pemohonIdx),
                    ]);
                }
            }

            // FUNGSI 1: TUKARKAN DATA AJK KEPADA PDF
            $app->load('committees');
            $pdf = Pdf::loadView('pdf.senarai_ajk', ['application' => $app]);
            
            $pdfFileName = 'senarai_ajk_' . strtolower($app->reference_no) . '.pdf';
            $pdfPath = 'dokumen_surau/' . $pdfFileName;

            Storage::disk('public')->put($pdfPath, $pdf->output());

            ApplicationDocument::create([
                'application_id' => $app->id,
                'jenis_dokumen'  => 'SALINAN_SENARAI_AJK',
                'fail_path'      => $pdfPath,
                'fail_nama_asal' => 'Senarai_AJK_' . $app->reference_no . '.pdf',
            ]);

            // Simpan Dokumen Sokongan
            $docs = ['ic_pengerusi', 'ic_setiausaha', 'ic_bendahari', 'pengesahan_kiblat'];
            foreach ($docs as $docKey) {
                if ($request->hasFile($docKey)) {
                    $path = $request->file($docKey)->store('dokumen_surau', 'public');
                    ApplicationDocument::create([
                        'application_id' => $app->id,
                        'jenis_dokumen'  => strtoupper($docKey),
                        'fail_path'      => $path,
                        'fail_nama_asal' => $request->file($docKey)->getClientOriginalName(),
                    ]);
                }
            }

            // Simpan Minit Mesyuarat Permohonan Awal
            if ($request->hasFile('minit_mesyuarat')) {
                $minitPath = $request->file('minit_mesyuarat')->store('minit_mesyuarat', 'public');
                ApplicationMeetingMinutes::create([
                    'application_id'    => $app->id,
                    'tahun_minit'       => date('Y'),
                    'fail_path'         => $minitPath,
                    'status_pengesahan' => 'DALAM_SEMAKAN'
                ]);
            }

            // =========================================================================
            // PERAKAMAN AUDIT LOG
            // =========================================================================
           /*  \App\Models\AuditLog::log(
                'HANTAR_PERMOHONAN', 
                "Permohonan pendaftaran surau baharu '{$app->nama_surau}' (No. Ref: {$app->reference_no}) telah berjaya dihantar oleh pemohon '{$pemohonNama}' ({$pemohonEmail})."
            ); */

            return $app;
        });

        // =========================================================================
        // FUNGSI 2: HANTAR E-MEL PEMAKLUMAN KEPADA SEMUA PENTADBIR MASJID
        // + CC JAWI + PEMOHON
        // =========================================================================
        try {

            // =========================================================
            // 1. DAPATKAN SEMUA PENTADBIR MASJID UNTUK MASJID INI
            // =========================================================
            $masjidUsers = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('slug', 'masjid');
            })
            ->whereDoesntHave('roles', function ($query) {
                // Pentadbir Sistem dikecualikan
                $query->where('slug', 'admin');
            })
            ->whereHas('masjids', function ($query) use ($createdData) {
                $query->where('masjids.id', $createdData->masjid_id);
            })
            ->where('is_active', true)
            ->whereNotNull('email')
            ->pluck('email')
            ->unique()
            ->values()
            ->toArray();


            // =========================================================
            // 2. PASTIKAN ADA PENTADBIR MASJID
            // =========================================================
            if (empty($masjidUsers)) {

                throw new \Exception(
                    'Tiada Pentadbir Masjid aktif ditemui untuk masjid ID: '
                    . $createdData->masjid_id
                );
            }


            // =========================================================
            // 3. DAPATKAN ADMIN JAWI
            // =========================================================
            $jawiEmails = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('slug', 'jawi');
            })
            ->where('is_active', true)
            ->whereNotNull('email')
            ->pluck('email')
            ->unique()
            ->values()
            ->toArray();


            // =========================================================
            // 4. CC JAWI + PEMOHON
            // =========================================================
            $ccEmails = $jawiEmails;

            if (
                !empty($createdData->pemohon_email) &&
                filter_var(
                    $createdData->pemohon_email,
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                $ccEmails[] = $createdData->pemohon_email;
            }


            // =========================================================
            // 5. BUANG EMAIL DUPLICATE
            // =========================================================
            $ccEmails = array_values(
                array_unique(
                    array_filter($ccEmails)
                )
            );


            // =========================================================
            // 6. SUBJECT EMAIL
            // =========================================================
            $subject =
                'Permohonan Pendaftaran Surau Baharu - '
                . $createdData->reference_no;


            // =========================================================
            // 7. HANTAR EMAIL
            // =========================================================
            $mailable = Mail::to($masjidUsers);

            if (!empty($ccEmails)) {
                $mailable->cc($ccEmails);
            }

            $mailable->send(
                new PermohonanBaharuMail($createdData)
            );


            // =========================================================
            // 8. REKOD EMAIL BERJAYA
            // =========================================================
            \App\Models\EmailLog::create([

                'application_id' => $createdData->id,

                'mail_type' => 'PERMOHONAN_BAHARU',

                'to_email' => json_encode($masjidUsers),

                'cc_email' => json_encode($ccEmails),

                'bcc_email' => null,

                'subject' => $subject,

                'status' => 'SENT',

                'error_message' => null,

                'sent_at' => now(),

            ]);


        } catch (\Exception $e) {


            // =========================================================
            // 9. REKOD EMAIL GAGAL
            // =========================================================
            \App\Models\EmailLog::create([

                'application_id' => $createdData->id,

                'mail_type' => 'PERMOHONAN_BAHARU',

                'to_email' => json_encode(
                    $masjidUsers ?? []
                ),

                'cc_email' => json_encode(
                    $ccEmails ?? []
                ),

                'bcc_email' => null,

                'subject' =>
                    $subject ??
                    'Permohonan Pendaftaran Surau Baharu',

                'status' => 'FAILED',

                'error_message' => $e->getMessage(),

                'sent_at' => null,

            ]);


            // =========================================================
            // 10. LARAVEL LOG
            // =========================================================
            \Illuminate\Support\Facades\Log::error(
                'Gagal menghantar e-mel permohonan baharu: '
                . $e->getMessage(),
                [
                    'application_id' => $createdData->id,
                    'reference_no' => $createdData->reference_no,
                ]
            );
        }

        return redirect()->route('portal')->with([
            'success'      => 'Permohonan pendaftaran surau anda telah berjaya dihantar untuk pengesahan Pentadbir Masjid & semakan JAWI.',
            'reference_no' => $createdData->reference_no
        ]);
    }

    // Semak Status Permohonan (Portal Awam)
    public function semakStatus(Request $request)
    {
        $request->validate([
            'reference_no' => 'required|string',
        ], [
            'reference_no.required' =>
                'Sila masukkan nombor rujukan permohonan.'
        ]);

        /*
        |--------------------------------------------------------------------------
        | CARI PERMOHONAN
        |--------------------------------------------------------------------------
        */

        $refNo = strtoupper(trim($request->reference_no));

        $application = Application::with([
            'meetingMinutes',
            'confirmations.confirmedBy',
            'masjidConfirmation.confirmedBy',
            'jawiConfirmation.confirmedBy',
            'zonConfirmation.confirmedBy',
            'masjid',
            'appointment',
            'appointment.ditetapkanOleh',
            'appointments',
            'appointments.ditetapkanOleh',
        ])
            ->where('reference_no', $refNo)
            ->first();


        /*
        |--------------------------------------------------------------------------
        | JIKA TIADA PERMOHONAN
        |--------------------------------------------------------------------------
        */

        if (!$application) {

            return back()->with(
                'error',
                'Nombor Rujukan Permohonan "' .
                $refNo .
                '" tidak ditemui dalam rekod.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | APPLICATION CONFIRMATIONS
        |--------------------------------------------------------------------------
        |
        | Digunakan untuk:
        |
        | - Pentadbir JAWI
        |
        */

        $confirmations = $application->confirmations
            ?? collect();


        /*
        |--------------------------------------------------------------------------
        | MASJID CONFIRMATION
        |--------------------------------------------------------------------------
        |
        | Peringkat 1 masih menggunakan MasjidConfirmation.
        |
        */

        $masjidConfirmation =
            $application->masjidConfirmation;


        /*
        |--------------------------------------------------------------------------
        | TAMBAHKAN MASJID CONFIRMATION KE COLLECTION
        |--------------------------------------------------------------------------
        |
        | Kita tidak mengubah database.
        |
        | Kita cuma masukkan rekod Pentadbir Masjid ke dalam
        | collection sementara supaya Blade boleh menggunakan
        | satu struktur yang sama.
        |
        */

        if ($masjidConfirmation) {

            /*
            | Tambahkan property peringkat secara runtime.
            */

            $masjidConfirmation->peringkat =
                'MASJID';


            /*
            | Masukkan rekod ke collection.
            */

            $confirmations->push(
                $masjidConfirmation
            );
        }


        /*
        |--------------------------------------------------------------------------
        | HANTAR KE BLADE
        |--------------------------------------------------------------------------
        */

        return view(
            'portal.status-permohonan',
            compact(
                'application',
                'confirmations'
            )
        );
    }


    // Upload semula minit mesyuarat yang ditolak
    public function uploadSemulaMinit(Request $request, $id)
    {
        $request->validate([
            'minit_mesyuarat' => 'required|file|mimes:pdf|max:5120',
        ]);

        $application = Application::findOrFail($id);

        if ($request->hasFile('minit_mesyuarat')) {

            // ============================================================
            // 1. SIMPAN FAIL
            // ============================================================
            $minitPath = $request->file('minit_mesyuarat')
                ->store('minit_mesyuarat', 'public');


            // ============================================================
            // 2. REKOD MINIT MESYUARAT
            // ============================================================
            $minit = ApplicationMeetingMinutes::create([
                'application_id'    => $application->id,
                'tahun_minit'       => date('Y'),
                'fail_path'         => $minitPath,
                'status_pengesahan' => 'DALAM_SEMAKAN',
                'catatan_semakan'   => null,
            ]);


            // ============================================================
            // 3. KEMBALIKAN STATUS PERMOHONAN
            // ============================================================
            if ($application->status === 'REJECTED') {
                $application->status = 'PENDING';
                $application->save();
            }


            // ============================================================
            // 4. EMAIL PENTADBIR JAWI
            // role slug = jawi
            // ============================================================
            $pentadbirJawi = User::whereHas('roles', function ($query) {
                $query->where('slug', 'jawi');
            })
            ->whereNotNull('email')
            ->pluck('email')
            ->toArray();


            // ============================================================
            // 5. EMAIL PENTADBIR SISTEM
            // role slug = admin
            // ============================================================
            $pentadbirSistem = User::whereHas('roles', function ($query) {
                $query->where('slug', 'admin');
            })
            ->whereNotNull('email')
            ->pluck('email')
            ->toArray();


            // ============================================================
            // 6. EMAIL PEMOHON
            // ============================================================
            $emailPemohon = null;

            if ($application->user && $application->user->email) {
                $emailPemohon = $application->user->email;
            }


            // ============================================================
            // 7. HANTAR EMAIL
            // JAWI = TO
            // ADMIN + PEMOHON = CC
            // ============================================================
            if (!empty($pentadbirJawi)) {

                $mail = Mail::to($pentadbirJawi)
                    ->cc($pentadbirSistem);

                if ($emailPemohon) {
                    $mail->cc($emailPemohon);
                }

                $mail->send(
                    new MinitMesyuaratDihantarSemulaMail(
                        $application,
                        $minit
                    )
                );
            }


            // ============================================================
            // 8. MESEJ BERJAYA
            // ============================================================
            return back()->with(
                'success',
                'Fail minit mesyuarat baharu telah berjaya dimuat naik dan dihantar untuk semakan semula pihak JAWI.'
            );
        }

        return back()->with(
            'error',
            'Gagal memuat naik fail. Sila cuba lagi.'
        );
    }
}