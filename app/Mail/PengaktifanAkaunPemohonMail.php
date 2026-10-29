<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PengaktifanAkaunPemohonMail extends Mailable
{
    use Queueable, SerializesModels;

    public Application $application;
    public User $user;
    public string $activationToken;
    public string $activationUrl;
    public string $temporaryPassword;

    public function __construct(
        Application $application,
        User $user,
        string $activationToken,
        ?string $activationUrl = null,
        string $temporaryPassword = 'password123'
    ) {
        $this->application = $application;
        $this->user = $user;
        $this->activationToken = $activationToken;
        $this->activationUrl = $activationUrl ?: route('account.activate', ['token' => $activationToken]);
        $this->temporaryPassword = $temporaryPassword;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pautan Pengaktifan Akaun Pentadbir Surau - ' . $this->application->nama_surau,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.pengaktifan-akaun',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
