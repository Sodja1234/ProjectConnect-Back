<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    /**
     * Canaux de livraison.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Représentation mail.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre mot de passe a été modifié')
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre mot de passe a été mis à jour avec succès.")
            ->line("Si vous n’êtes pas à l’origine de cette modification, veuillez réinitialiser votre mot de passe immédiatement ou contacter le support.")
            ->line("Merci d'utiliser notre plateforme.");
    }

    /**
     * Représentation base de données.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Mot de passe mis à jour',
            'message' => 'Votre mot de passe a été modifié avec succès.',
        ];
    }
}
