<?php

namespace App\Mail;

use App\Models\SurauAgmJemputan;
use App\Models\SurauAgmMesyuarat;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Jemputan ke Mesyuarat Agung Tahunan surau.
 * Kandungan e-mel ialah surat jemputan, dan surat yang sama dilampirkan dalam format PDF.
 */
class JemputanAgmMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array{nama:string, jawatan:string, no_tel:?string, email:?string} */
    public array $pengirim;

    public function __construct(
        public SurauAgmMesyuarat $mesyuarat,
        public SurauAgmJemputan $jemputan,
        ?User $pengirim = null,
    ) {
        $this->mesyuarat->loadMissing('sesi.surau.masjid');
        $this->pengirim = self::maklumatPengirim($this->mesyuarat, $pengirim);
    }

    /** Nama, jawatan AJK (jika tersenarai), telefon dan e-mel pengirim */
    public static function maklumatPengirim(SurauAgmMesyuarat $mesyuarat, ?User $user): array
    {
        $surau = $mesyuarat->sesi->surau;
        $user ??= $surau->user;

        $ic = preg_replace('/\D+/', '', (string) $user?->no_kad_pengenalan);
        $ajk = $ic === '' ? null : $surau->committees->first(
            fn ($c) => preg_replace('/\D+/', '', (string) $c->ic_no) === $ic
        );

        return [
            'nama'    => $user?->name ?? 'Pentadbir Surau',
            'jawatan' => $ajk ? ($ajk->jawatan === 'AJK' ? 'Ahli Jawatankuasa' : $ajk->jawatan) : 'Pentadbir Surau',
            'no_tel'  => $user?->no_telefon_bimbit ?: $user?->no_telefon_pejabat,
            'email'   => $user?->email,
        ];
    }

    public function envelope(): Envelope
    {
        $surau = $this->mesyuarat->sesi->surau;

        return new Envelope(
            subject: "Jemputan ke Mesyuarat Agung Tahunan {$surau->nama_surau} Bil. {$this->mesyuarat->bilangan} Tahun {$this->mesyuarat->tahun}",
            replyTo: filter_var($this->pengirim['email'], FILTER_VALIDATE_EMAIL) ? [$this->pengirim['email']] : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.surau.jemputan-agm');
    }

    public function attachments(): array
    {
        $nama = "Surat_Jemputan_AGM_Bil{$this->mesyuarat->bilangan}_{$this->mesyuarat->tahun}.pdf";

        return [
            Attachment::fromData(fn () => $this->pdf(), $nama)->withMime('application/pdf'),
        ];
    }

    public function pdf(): string
    {
        return Pdf::loadView('pdf.surat-jemputan-agm', [
            'mesyuarat' => $this->mesyuarat,
            'jemputan'  => $this->jemputan,
            'pengirim'  => $this->pengirim,
        ])->setPaper('a4', 'portrait')->output();
    }
}
