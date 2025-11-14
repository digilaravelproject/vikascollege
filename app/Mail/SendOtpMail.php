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

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp, $name;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $name = null)
    {
        $this->otp = $otp;
        $this->name = $name;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {    // 👇 FIX: Apply configuration when the job is executed by the worker.
        MailConfigService::applyFromDb();
        return new Envelope(
            subject: 'Your Verification OTP',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            // Assuming the view is located at resources/views/emails/otp.blade.php
            view: 'emails.otp',
            with: [
                'otp' => $this->otp,
                'name' => $this->name,
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
