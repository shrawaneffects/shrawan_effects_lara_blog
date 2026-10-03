<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SecurityAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $actionTitle;
    public string $actionDescription;
    public string $ipAddress;
    public string $timestamp;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $actionTitle, string $actionDescription, ?string $ipAddress = null)
    {
        $this->user = $user;
        $this->actionTitle = $actionTitle;
        $this->actionDescription = $actionDescription;
        $this->ipAddress = $ipAddress ?: request()->ip();
        $this->timestamp = now()->timezone(config('app.timezone', 'Asia/Kolkata'))->format('d M Y, h:i A T');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Security Alert: ' . $this->actionTitle . ' - ' . \App\Models\Setting::get('site_name', config('app.name', 'Shrawan Effects')),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.security_alert',
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
