<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $loginUrl;
    public string $ipAddress;
    public int $expiryMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $loginUrl, ?string $ipAddress = null, int $expiryMinutes = 15)
    {
        $this->user = $user;
        $this->loginUrl = $loginUrl;
        $this->ipAddress = $ipAddress ?: request()->ip();
        $this->expiryMinutes = $expiryMinutes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $siteName = \App\Models\Setting::get('site_name', config('app.name', 'Shrawan Effects'));
        return new Envelope(
            subject: 'Your Magic Sign-in Link - ' . $siteName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.magic_link',
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
