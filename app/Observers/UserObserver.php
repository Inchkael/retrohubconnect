<?php

namespace App\Observers;

use App\Models\User;
use App\Services\GeocodingService;

/**
 * UserObserver - Observer pour le modèle User
 *
 * Cet observer écoute les événements du cycle de vie du modèle User
 * et exécute des actions automatiques comme le géocodage des adresses.
 *
 * Il est enregistré dans AppServiceProvider via User::observe(UserObserver::class)
 */

class UserObserver
{
    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    /**
     * Handle the User "saving" event.
     */
    // app/Observers/UserObserver.php
    public function saving(User $user)
    {
        // Géocoder seulement si l'adresse a changé ou si les coordonnées sont manquantes
        if ($user->isDirty('address') && $user->address && (!$user->latitude || !$user->longitude)) {
            try {
                $result = $this->geocodingService->geocodeAddress($user->address);
                if ($result) {
                    $user->latitude = $result['latitude'];
                    $user->longitude = $result['longitude'];
                }
            } catch (\Exception $e) {
                \Log::error("Erreur de géocodage pour l'utilisateur {$user->id}: " . $e->getMessage());
            }
        }
    }
}
