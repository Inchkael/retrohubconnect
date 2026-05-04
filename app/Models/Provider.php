<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Provider
 *
 * Ce modèle représente un prestataire dans le système. Il étend le modèle User
 * en ajoutant des informations spécifiques aux prestataires de services.
 *
 * Un prestataire est un utilisateur spécialisé qui offre des services via la plateforme.
 * Ce modèle est lié au modèle User par une relation one-to-one, où chaque prestataire
 * est associé à un utilisateur standard.
 *
 * Fonctionnalités principales :
 * - Gestion des informations professionnelles des prestataires
 * - Relation avec le modèle User (utilisateur associé)
 * - Gestion des photos du prestataire
 * - Stockage des coordonnées géographiques pour la localisation
 */

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'description',
        'address',
        'phone',
        'vat_number',
        'website',
        'logo',
        'latitude',
        'longitude'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->hasMany(ProviderPhoto::class);
    }
}
