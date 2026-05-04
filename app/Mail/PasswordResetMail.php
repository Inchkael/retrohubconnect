<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Classe PasswordResetMail
 *
 * Cette classe représente un email de réinitialisation de mot de passe.
 * Elle étend la classe Mailable de Laravel et utilise les traits Queueable et SerializesModels
 * pour permettre la mise en file d'attente et la sérialisation du mail.
 *
 * Fonctionnalités principales :
 * - Envoi d'emails de réinitialisation de mot de passe
 * - Mode "bypass" pour simuler l'envoi d'emails en environnement de développement
 * - Personnalisation du contenu de l'email avec les informations de l'utilisateur et le token
 * - Utilisation de vues Blade pour le contenu de l'email
 *
 * Processus typique :
 * 1. Un utilisateur demande une réinitialisation de mot de passe
 * 2. Un token de réinitialisation est généré
 * 3. Une instance de cette classe est créée avec l'utilisateur et le token
 * 4. L'email est envoyé à l'utilisateur avec un lien contenant le token
 * 5. L'utilisateur clique sur le lien pour réinitialiser son mot de passe
 */

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        // Si le mode bypass est activé, ne pas envoyer d'email
        if (config('mail.bypass_sending')) {
            // Log pour indiquer que l'email est simulé
            info("Email de réinitialisation de mot de passe simulé pour : {$this->user->email}");
            // Retourne une vue vide ou un message de log
            return $this->view('emails.empty');
        }

        // Sinon, envoyer l'email normalement
        return $this->subject('Réinitialisez votre mot de passe')
            ->view('emails.password_reset')
            ->with([
                'user' => $this->user,
                'token' => $this->token,
            ]);
    }
}
