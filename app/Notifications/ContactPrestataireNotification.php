<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notification de contact pour les prestataires
 *
 * Cette classe gère l'envoi des notifications par email lorsqu'un utilisateur
 * contacte un prestataire via la plateforme. Elle étend la classe Notification
 * de Laravel et utilise le trait Queueable pour permettre la mise en file d'attente
 * des notifications.
 *
 * Le processus de notification :
 * 1. Un utilisateur remplit un formulaire de contact sur la fiche d'un prestataire
 * 2. Une instance de cette notification est créée avec les informations du message
 * 3. La notification est envoyée au prestataire par email
 * 4. Le prestataire reçoit un email avec les détails du message et un lien
 *    vers sa fiche pour répondre
 */

class ContactPrestataireNotification extends Notification
{
    use Queueable;

    protected $nom;
    protected $email;
    protected $objet;
    protected $message;
    protected $lien;

    public function __construct($nom, $email, $objet, $message, $lien)
    {
        $this->nom = $nom;
        $this->email = $email;
        $this->objet = $objet;
        $this->message = $message;
        $this->lien = $lien;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("[Annuaire Bien-Être] Nouveau message de {$this->nom}")
            ->greeting("Bonjour, vous avez reçu un nouveau message via l'annuaire.")
            ->line("**De** : {$this->nom} ({$this->email})")
            ->line("**Objet** : {$this->objet}")
            ->line("**Message** :")
            ->line($this->message)
            ->action('Voir la fiche prestataire', $this->lien)
            ->line("Merci d'utiliser notre plateforme !");
    }
}
