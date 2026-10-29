<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Models\ApplicationMeetingMinutes;

class MinitMesyuaratDitolakMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $minit;
    public $catatan;

    /**
     * Terima data permohonan, minit, dan catatan dari controller.
     */
    public function __construct(Application $application, ApplicationMeetingMinutes $minit, $catatan = null)
    {
        $this->application = $application;
        $this->minit = $minit;
        $this->catatan = $catatan;
    }

    /**
     * Tetapkan Tajuk / Subjek E-mel.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "PERHATIAN: Kemaskini Minit Mesyuarat Bagi Permohonan {$this->application->reference_no}",
        );
    }

    /**
     * Tetapkan Templat Blade untuk E-mel.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.minit_ditolak',
        );
    }

    /**
     * Lampiran E-mel (Jika Ada).
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}