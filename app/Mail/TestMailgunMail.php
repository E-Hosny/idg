<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMailgunMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $sentAt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'IDG — Mailgun test email',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.test-mailgun',
        );
    }
}
