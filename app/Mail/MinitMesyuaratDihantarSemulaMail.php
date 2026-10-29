<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Models\ApplicationMeetingMinutes;

class MinitMesyuaratDihantarSemulaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $minit;

    public function __construct(
        Application $application,
        ApplicationMeetingMinutes $minit
    ) {
        $this->application = $application;
        $this->minit = $minit;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "MINIT MESYUARAT DIHANTAR SEMULA: {$this->application->reference_no}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.minit_dihantar_semula',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}