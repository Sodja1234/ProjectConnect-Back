<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;


class ProjectInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $email;
    public string $token;
    public $projectRole;
    public string $invitationUrl;

    public function __construct(string $email, $projectRole,$token)
    {
        $this->email = $email;
        $this->projectRole = $projectRole;
        $this->token = $token;
        $this->invitationUrl = config('app.frontend_url') . '/register?email=' . urlencode($email) . '&token=' . $token;
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'invitation',
            to: [$this->email]
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.project_invitation',
            with: [
                'email' => $this->email,
                'projectRole' => $this->projectRole,
                'token' => $this->token,
                'invitationUrl' => $this->invitationUrl,
            ]
        );
    }








}
