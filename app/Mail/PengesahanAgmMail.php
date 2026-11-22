<?php

namespace App\Mail;

use App\Models\SurauAgmMesyuarat;
use App\Models\SurauAgmPengesahan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Keputusan pengesahan JAWI bagi Keputusan Mesyuarat AGM, kepada Pentadbir Surau semasa.
 */
class PengesahanAgmMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SurauAgmMesyuarat $mesyuarat,
        public SurauAgmPengesahan $pusingan,
    ) {
    }

    public function envelope(): Envelope
    {
        $surau  = $this->mesyuarat->sesi->surau->nama_surau;
        $status = $this->mesyuarat->status_pengesahan === 'DISAHKAN' ? 'Disahkan' : 'Perlu Pembetulan';

        return new Envelope(subject: "Pengesahan Mesyuarat Agung Tahunan Bil. {$this->mesyuarat->bilangan} ({$this->mesyuarat->tahun}): {$status} - {$surau}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.surau.pengesahan-agm', with: [
            'url' => route('surau.agm.pengesahan.index', $this->mesyuarat),
        ]);
    }
}
