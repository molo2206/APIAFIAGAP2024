<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PositionnementGap extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct(

        public $email,
        public $phone,
        public $full_name,
        public $qte,
        public $name,
        public $gapid,
        public $gapidvalide,
        public $datagap_appui,
        public $budget_disponible,
        public $devise,
        public $date,

    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Positionnement Partennaire sur structure' ." ". $this-> name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.positionnement',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
