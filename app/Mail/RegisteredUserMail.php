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

    public function __construct(
        public User $user,
       public $token
    ) {}

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
                'token' => $this->token
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
