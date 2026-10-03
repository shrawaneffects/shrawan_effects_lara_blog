<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $code;
    public string $email;
    public int $expiryMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct(string $name, string $code, string $email, int $expiryMinutes = 10)
    {
        $this->name = $name;
        $this->code = $code;
        $this->email = $email;
        $this->expiryMinutes = $expiryMinutes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Registration Verification Code: ' . $this->code . ' - ' . \App\Models\Setting::get('site_name', config('app.name', 'Shrawan Effects')),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.register_otp',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
