<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\ApplicationAppointment;
use App\Services\CalendarInviteService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemujanjiLawatanTapakMail extends Mailable
{
    use Queueable, SerializesModels;

    public Application $application;
    public ApplicationAppointment $appointment;

    public function __construct(Application $application, ApplicationAppointment $appointment)
    {
        $this->application = $application;
        $this->appointment = $appointment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Temujanji Lawatan Tapak Permohonan Surau - '
                . $this->application->reference_no,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.permohonan.temujanji-lawatan-tapak',
            with: [
                'googleCalendarUrl' => CalendarInviteService::generateGoogleCalendarUrl(
                    $this->application,
                    $this->appointment
                ),
            ],
        );
    }

    public function attachments(): array
    {
        $icsContent = CalendarInviteService::generateIcs($this->application, $this->appointment);

        return [
            Attachment::fromData(fn () => $icsContent, 'temujanji-lawatan-tapak.ics')
                ->withMime('text/calendar; charset=UTF-8; method=REQUEST'),
        ];
    }
}
