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

    public string $otp;

    public function __construct(
        public User $user,
        public ?string $token = null,
    ) {
        $this->otp = (string)rand(100000, 999999);
        $this->user->update([
            'email_otp' => $this->otp,
            'email_otp_expires_at' => now()->addMinutes(10),
        ]);
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Activation de votre compte',
            to: [$this->user->email]
        );
    }

    public function content(): Content
    {
        $url = $this->generateVerificationUrl();

        return new Content(
            markdown: 'mail.registered-user-mail',
            with: [
                'user' => $this->user,
                'url' => $url,
                'token' => $this->token,
                'otp' => $this->otp
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }

    private function generateVerificationUrl(): string
    {
        $userId = $this->user->id;
        $hash = sha1($this->user->email);
        $token = $this->token;

        // Si le token est null, on évite de l'ajouter
        if ($this->token) {
            return config('app.frontend_url') . "/verify-email/{$userId}/{$hash}/?token={$token}";
        }

        // URL alternative si aucun token n'est fourni
        return config('app.frontend_url') . "/verify-email/{$userId}/{$hash}";
    }
}
