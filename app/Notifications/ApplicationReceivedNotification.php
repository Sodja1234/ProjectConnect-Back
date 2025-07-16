<?php

namespace App\Notifications;

use App\Models\Candidacy;
use App\Models\ProjectRole;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Candidacy $candidacy,
        private ProjectRole $projectRole
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $candidateName = $this->candidacy->user->name ?? 'Un candidat';
        $roleName = $this->projectRole->role->name ?? 'un rôle';
        $jobTitle = $this->projectRole->project->title ?? 'votre projet';
        $url = $this->getUrlDetails();

        return (new MailMessage)
            ->subject("Nouvelle candidature pour le rôle « {$roleName} » sur {$jobTitle}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("{$candidateName} a postulé au rôle **{$roleName}** sur le projet **{$jobTitle}**.")
            ->line("Vous pouvez consulter sa candidature dès maintenant.")
            ->action('Voir la candidature', $url)
            ->line("Merci d'utiliser notre plateforme.");
    }

    /**
     * Get the array representation of the notification (for database).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $candidateName = $this->candidacy->user->name ?? 'Un candidat';
        $roleName = $this->projectRole->role->name ?? 'un rôle';
        $jobTitle = $this->projectRole->project->title ?? 'votre projet';

        return [
            'candidacy_id' => $this->candidacy->id,
            'title' => "Candidature pour le rôle « {$roleName} »",
            'message' => "{$candidateName} a postulé au rôle « {$roleName} » sur le projet « {$jobTitle} ».",
            'link' => $this->getUrlDetails(),
        ];
    }

    private function getUrlDetails(): string
    {
        return url(config('app.frontend_url') . "/project/{$this->projectRole->project->slug}");
    }
}
