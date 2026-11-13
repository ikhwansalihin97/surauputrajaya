<?php

namespace App\Mail;

use App\Models\SurauPertukaranPentadbir;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mel serah tugas Pentadbir Surau.
 * PERMINTAAN: kepada calon (pautan pengaktifan akaun baharu, atau log masuk). DITERIMA / DITOLAK: kepada pentadbir lama.
 */
class PertukaranPentadbirMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SurauPertukaranPentadbir $pertukaran,
        public string $jenis,
    ) {
    }

    public function envelope(): Envelope
    {
        $surau = $this->pertukaran->surau->nama_surau;

        return new Envelope(subject: match ($this->jenis) {
            'DITERIMA' => "Serah Tugas Pentadbir Surau Diterima - {$surau}",
            'DITOLAK'  => "Serah Tugas Pentadbir Surau Ditolak - {$surau}",
            default    => "Permintaan Serah Tugas Pentadbir Surau - {$surau}",
        });
    }

    public function content(): Content
    {
        $token = $this->pertukaran->kepada?->activation_token;

        return new Content(view: 'emails.surau.pertukaran-pentadbir', with: [
            'perluAktif' => (bool) $token,
            'url'        => $token ? route('account.activate', $token) : route('login'),
        ]);
    }
}
