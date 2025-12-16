<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp, $name;

    public function __construct($otp, $name = null)
    {
        $this->otp = $otp;
        $this->name = $name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Verification OTP');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: ['otp' => $this->otp, 'name' => $this->name],
        );
    }

    public function attachments(): array { return []; }
}