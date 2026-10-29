<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PermohonanDisahkanZonMail extends Mailable
{
    use Queueable, SerializesModels;

    public Application $application;
    public ?string $catatan;

    public function __construct(Application $application, ?string $catatan = null)
    {
        $this->application = $application;
        $this->catatan = $catatan;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permohonan Pendaftaran Surau Telah Diluluskan - '
                . $this->application->reference_no,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.permohonan.disahkan-zon',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}