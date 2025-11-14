<?php

namespace App\Mail;

use App\Services\MailConfigService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class EnquiryMailToAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $enquiry;

    /**
     * Create a new message instance.
     */
    public function __construct($enquiry)
    {
        $this->enquiry = $enquiry;
        MailConfigService::applyFromDb();
    }

    /**
     * Get the message envelope (defines the subject).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Website Enquiry',
        );
    }

    /**
     * Get the message content definition (defines the view and view data).
     */
    public function content(): Content
    {
        return new Content(
            // Assuming the view is located at resources/views/emails/enquiry_admin.blade.php
            view: 'emails.enquiry_admin',
            with: [
                'enquiry' => $this->enquiry,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
