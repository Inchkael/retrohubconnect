<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Classe EmailConfirmationMail
 *
 * Cette classe représente un email de confirmation d'adresse email.
 * Elle étend la classe Mailable de Laravel et utilise les traits Queueable et SerializesModels
 * pour permettre la mise en file d'attente et la sérialisation du mail.
 *
 * Fonctionnalités principales :
 * - Envoi d'emails de confirmation pour les nouvelles inscriptions
 * - Mode "bypass" pour simuler l'envoi d'emails en environnement de développement
 * - Personnalisation du contenu de l'email avec les informations de l'utilisateur
 * - Utilisation de vues Blade pour le contenu de l'email
 *
 * Processus typique :
 * 1. Un nouvel utilisateur s'inscrit sur la plateforme
 * 2. Une instance de cette classe est créée avec l'utilisateur
 * 3. L'email de confirmation est envoyé à l'utilisateur avec un lien de confirmation
 * 4. L'utilisateur clique sur le lien pour confirmer son adresse email
 */

class EmailConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        // Si le mode bypass est activé, ne pas envoyer d'email
        if (config('mail.bypass_sending')) {
            // Log pour indiquer que l'email est simulé
            info("Email de confirmation simulé pour : {$this->user->email}");
            // Retourne une vue vide ou un message de log
            return $this->view('emails.empty');
        }

        // Sinon, envoyer l'email normalement
        return $this->subject('Confirmez votre email')
            ->view('emails.confirmation')
            ->with(['user' => $this->user]);
    }
}
