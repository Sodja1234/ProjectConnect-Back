<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class RegisteredUserMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(public User $user)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Activation de votre compte',
            to: [$this->user->email]

        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $url = $this->getUrlSigned();

        return new Content(
            markdown: 'mail.registered-user-mail',
            with: [
                'user' => $this->user,
                'url' => $url,
            ]
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

    private function getUrlSigned(): string
    {
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $this->user->id,
                'hash' => sha1($this->user->email),
            ]
        );

        $encodedSignedUrl = urlencode($signedUrl);

        return config('app.frontend_url') . '/verify-email?callback=' . $encodedSignedUrl;
    }

}
