<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Modèle ProviderPhoto
 *
 * Ce modèle représente les photos associées à un prestataire (provider).
 * Il permet de gérer les photos de profil ou de galerie des prestataires
 * inscrits sur la plateforme.
 *
 * Fonctionnalités principales :
 * - Stockage du chemin vers les photos
 * - Relation avec le modèle Provider (prestataire)
 * - Utilisation du trait HasFactory pour la génération de données de test
 */
class ProviderPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'photo_path'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
