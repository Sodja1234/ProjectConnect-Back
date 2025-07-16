<?php

namespace App\Notifications;

use App\Models\Candidacy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CandidacyStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param Candidacy $candidacy
     * @param string $status Status of the application ('accepted' or 'rejected')
     */
    public function __construct(
        private Candidacy $candidacy,
        private string $applicationStatus
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $candidateFullName = $this->candidacy->user->name ?? 'Candidat';
        $projectTitle = $this->candidacy->projectRole->project->title ?? 'votre projet';
        $roleName = $this->candidacy->projectRole->role->name ?? 'votre rôle';

        if ($this->applicationStatus === 'accepted') {
            $subject = "Votre candidature pour « {$roleName} » dans « {$projectTitle} » a été acceptée !";
            $greetingLine = "Félicitations {$candidateFullName}, votre candidature pour le poste de « {$roleName} » a été acceptée.";
            $actionButtonText = "Voir les détails";
        } else {
            $subject = "Votre candidature pour « {$roleName} » dans « {$projectTitle} » a été refusée";
            $greetingLine = "Bonjour {$candidateFullName}, nous sommes désolés de vous informer que votre candidature pour le poste de « {$roleName} » a été refusée.";
            $actionButtonText = "Voir votre candidature";
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Bonjour {$candidateFullName},")
            ->line($greetingLine)
            ->action($actionButtonText, $this->getCandidacyUrl())
            ->line('Merci d’utiliser notre plateforme.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $projectTitle = $this->candidacy->projectRole->project->title ?? 'votre projet';
        $roleName = $this->candidacy->projectRole->role->name ?? 'votre rôle';

        return [
            'title' => $projectTitle,
            'message' => $this->applicationStatus === 'accepted'
                ? "Votre candidature pour le poste de « {$roleName} » dans « {$projectTitle} » a été acceptée."
                : "Votre candidature pour le poste de « {$roleName} » dans « {$projectTitle} » a été refusée.",
            'link' => $this->getCandidacyUrl(),
        ];
    }

    /**
     * Generate the URL to view the candidacy details.
     *
     * @return string
     */
    private function getCandidacyUrl(): string
    {
        return url(config('app.frontend_url') . "/candidacy/{$this->candidacy->id}");
    }
}
