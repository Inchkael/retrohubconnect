<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

/**
 * Notification de confirmation d'email
 *
 * Cette classe gère l'envoi des emails de confirmation pour les nouveaux utilisateurs.
 * Elle étend la classe Notification de Laravel et utilise le trait Queueable
 * pour permettre la mise en file d'attente des notifications.
 *
 * Fonctionnalités principales :
 * - Envoi d'email de confirmation avec un lien de validation
 * - Mode "bypass" pour simuler l'envoi d'email en environnement de développement
 * - Personnalisation du message de confirmation
 */

class EmailConfirmationNotification extends Notification
{
    use Queueable;



    public function __construct()
    {
        //
    }
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Si le mode bypass est activé, ne pas envoyer l'email
        if (Config::get('mail.bypass_sending')) {
            Log::info('Email de confirmation simulé pour : ' . $notifiable->email);
            return (new MailMessage)->subject('Confirmation simulée');
        }

        // Génère l'URL de confirmation
        $url = route('confirm.email', $notifiable->confirmation_token);

        // Sinon, envoyer l'email normalement
        return (new MailMessage)
            ->subject('Confirmez votre email')
            ->greeting('Bonjour ' . $notifiable->first_name . ',')
            ->line('Veuillez cliquer sur le bouton ci-dessous pour valider votre adresse email.')
            ->action('Confirmer mon email', $url)
            ->line('Si vous n\'avez pas créé de compte, ignorez simplement ce message.')
            ->salutation('Cordialement,<br>L\'équipe Espace de Mickael Collings');
   }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }


}
