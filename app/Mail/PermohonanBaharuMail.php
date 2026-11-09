<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;

class PermohonanBaharuMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->application->isRevision()
                ? "PERMOHONAN KEMASKINI: Kemaskini Maklumat Surau {$this->application->reference_no}"
                : "PERMOHONAN BAHARU: Permohonan Pendaftaran Surau {$this->application->reference_no}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.permohonan_baharu',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}