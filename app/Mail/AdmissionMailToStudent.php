<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdmissionMailToStudent extends Mailable
{
    use Queueable, SerializesModels;

    public $admission;

    public function __construct($admission)
    {
        $this->admission = $admission;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Application Received');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admission_student',
            with: ['admission' => $this->admission],
        );
    }

    public function attachments(): array { return []; }
}