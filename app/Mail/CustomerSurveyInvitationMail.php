<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerSurveyInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $customerName,
        public string $surveyUrl,
        public ?string $receivingRecordNo = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'IDG — استبيان رضا العملاء | Customer Feedback',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer-survey-invitation',
        );
    }
}
