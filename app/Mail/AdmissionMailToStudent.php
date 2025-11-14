<?php

namespace App\Mail;

use App\Services\MailConfigService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content; // Required for content()
use Illuminate\Mail\Mailables\Envelope; // Required for envelope()
use Illuminate\Queue\SerializesModels;

class AdmissionMailToStudent extends Mailable
{
    use Queueable, SerializesModels;

    public $admission; // Public property is automatically shared with the view

    /**
     * Create a new message instance.
     */
    public function __construct($admission)
    {
        $this->admission = $admission;
        MailConfigService::applyFromDb();
    }

    /**
     * Get the message envelope (defines the subject, sender, and recipients).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // Subject defined here
            subject: 'Application Received',
        );
    }

    /**
     * Get the message content definition (defines the view and view data).
     */
    public function content(): Content
    {
        return new Content(
            // View file path: resources/views/emails/admission_student.blade.php
            view: 'emails.admission_student',

            // Explicitly passing data to the view
            with: [
                'admission' => $this->admission,
            ],
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
