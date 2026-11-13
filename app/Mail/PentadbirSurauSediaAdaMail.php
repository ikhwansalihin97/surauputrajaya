<?php

namespace App\Mail;

use App\Models\Surau;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Pemakluman kepada Pentadbir Surau bagi surau sedia ada yang didaftarkan oleh JAWI.
 * Pengguna baharu menerima pautan pengaktifan; pengguna sedia ada dimaklumkan sahaja.
 */
class PentadbirSurauSediaAdaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Surau $surau,
        public User $user,
        public ?string $activationUrl = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ($this->activationUrl ? 'Pautan Pengaktifan Akaun Pentadbir Surau - ' : 'Pendaftaran Sebagai Pentadbir Surau - ')
                . $this->surau->nama_surau,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.auth.pentadbir-surau-sedia-ada');
    }
}
