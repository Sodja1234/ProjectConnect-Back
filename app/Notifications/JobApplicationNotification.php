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
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $jobTitle = $this->projectRole->project->title ?? 'le poste';

        $url = url(config('app.frontend_url') . "/candidacy/{$this->candidacy->id}");

        return (new MailMessage)
            ->subject("Votre candidature a été envoyée avec succès")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Nous confirmons que votre candidature pour le poste de **{$jobTitle}** a bien été envoyée.")
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
        $jobTitle = $this->projectRole->project->title ?? 'le poste';

        return [
            'candidacy_id' => $this->candidacy->id,
            'title' => $jobTitle,
            'message' => "Votre candidature pour le projet  \"{$jobTitle}\" a été envoyée avec succès.",
        ];
    }
}
