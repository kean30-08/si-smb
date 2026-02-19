<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BroadcastAgendaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdfContent;

    public function __construct($pdfContent)
    {
        $this->pdfContent = $pdfContent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pemberitahuan Jadwal Sekolah Minggu & Puja Bakti',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.agenda_pesan', // Kita akan buat file view ini nanti
        );
    }

    public function attachments(): array
    {
        // Menyisipkan file PDF langsung dari memory (tanpa harus disimpan ke folder PC dulu)
        return [
            Attachment::fromData(fn () => $this->pdfContent, 'Jadwal_Kegiatan_Vihara.pdf')
                ->withMime('application/pdf'),
        ];
    }
}