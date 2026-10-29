<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PermohonanDisahkanJawiMail extends Mailable
{
    use Queueable, SerializesModels;

    public Application $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permohonan Menunggu Semakan Pentadbir Zon - '
                . $this->application->reference_no,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.permohonan.disahkan-jawi',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}