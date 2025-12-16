<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnquiryMailToAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $enquiry;

    public function __construct($enquiry)
    {
        $this->enquiry = $enquiry;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New Website Enquiry');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.enquiry_admin',
            with: ['enquiry' => $this->enquiry],
        );
    }

    public function attachments(): array { return []; }
}