<?php

namespace App\Notifications;

use App\Models\Candidacy;
use App\Models\ProjectRole;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Candidacy $candidacy,
        private ProjectRole $projectRole
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $jobTitle = $this->projectRole->project->title ?? 'le projet';
        $roleName = $this->projectRole->role->name ?? 'le rôle';
        $url = $this->getUrlDetails();

        return (new MailMessage)
            ->subject("Votre candidature au rôle « {$roleName} » a été envoyée")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Nous confirmons que votre candidature pour le rôle **{$roleName}** sur le projet **{$jobTitle}** a bien été envoyée.")
            ->line("Nous étudierons votre profil avec attention et vous tiendrons informé(e) des prochaines étapes.")
            ->action('Voir votre candidature', $url)
            ->line("Merci de votre confiance et bonne chance !");
    }

    /**
     * Représentation de la notification pour stockage en base.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $jobTitle = $this->projectRole->project->title ?? 'le projet';
        $roleName = $this->projectRole->role->name ?? 'le rôle';

        return [
            'candidacy_id' => $this->candidacy->id,
            'title' => "Candidature au rôle « {$roleName} »",
            'message' => "Votre candidature au rôle « {$roleName} » sur le projet \"{$jobTitle}\" a été envoyée avec succès.",
            'link' => $this->getUrlDetails(),
        ];
    }

    private function getUrlDetails(): string
    {
        return url(config('app.frontend_url') . "/project/{$this->projectRole->project->slug}");
    }
}
