<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification de réinitialisation de mot de passe
 *
 * Cette classe gère l'envoi des notifications par email pour la réinitialisation
 * de mot de passe. Elle étend la classe Notification de Laravel et utilise le
 * trait Queueable pour permettre la mise en file d'attente des notifications.
 *
 * Le processus de réinitialisation de mot de passe:
 * 1. L'utilisateur demande une réinitialisation via un formulaire
 * 2. Un token unique est généré et associé à l'utilisateur
 * 3. Cette notification est envoyée avec le token dans un lien
 * 4. L'utilisateur clique sur le lien et est redirigé vers un formulaire de réinitialisation
 * 5. Le token est validé et le mot de passe peut être changé
 */

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe')
            ->line('Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.')
            ->action('Réinitialiser le mot de passe', $url)
            ->line('Ce lien expirera dans :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')])
            ->line('Si vous n\'avez pas demandé de réinitialisation de mot de passe, aucune autre action n\'est requise.');
    }
}
