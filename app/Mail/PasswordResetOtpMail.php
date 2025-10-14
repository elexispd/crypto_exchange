<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $name;
    public $validityMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $name, $validityMinutes = 10)
    {
        $this->otp = $otp;
        $this->name = $name;
        $this->validityMinutes = $validityMinutes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('no_reply@coafcare.online', 'Coafcare'),
            subject: 'Password Reset OTP - Coafcare',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.user.password-reset-otp',
            with: [
                'otp' => $this->otp,
                'name' => $this->name,
                'validityMinutes' => $this->validityMinutes,
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
