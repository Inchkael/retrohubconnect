<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\ServiceCategory;

/**
 * Notification de transfert de catégorie de service
 *
 * Cette classe gère l'envoi des notifications par email lorsqu'un prestataire
 * est transféré d'une catégorie de service à une autre. Elle étend la classe
 * Notification de Laravel et utilise le trait Queueable pour permettre la mise
 * en file d'attente des notifications.
 *
 * Contexte d'utilisation :
 * - Lorsqu'un administrateur transfère un prestataire d'une catégorie à une autre
 * - La notification informe le prestataire du changement effectué
 * - Cela permet de maintenir une bonne communication avec les prestataires
 *   et de les informer des modifications administratives
 */

class CategoryTransferNotification extends Notification
{
    use Queueable;

    protected $oldCategory;
    protected $newCategory;

    public function __construct(ServiceCategory $oldCategory, ServiceCategory $newCategory)
    {
        $this->oldCategory = $oldCategory;
        $this->newCategory = $newCategory;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre catégorie de service a été modifiée')
            ->greeting('Bonjour,')
            ->line("Votre catégorie de service a été transférée de **{$this->oldCategory->name}** vers **{$this->newCategory->name}**.")
            ->line("Cordialement,")
            ->line("L'équipe Bien-Être");
    }
}
