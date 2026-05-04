<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle AddressMapping
 *
 * Ce modèle représente un mapping entre une adresse originale et ses coordonnées géographiques.
 * Il est utilisé pour stocker les résultats de géocodage afin d'éviter de refaire
 * des appels répétés à l'API de géocodage pour les mêmes adresses.
 *
 * Fonctionnalités principales :
 * - Stockage des adresses originales et nettoyées
 * - Mémorisation des coordonnées géographiques (latitude, longitude)
 * - Optimisation des performances en évitant les appels API répétés
 * - Gestion des adresses mal formatées ou ambiguës
 *
 * Cas d'utilisation typiques :
 * - Amélioration des performances de géocodage en mettant en cache les résultats
 * - Correction des adresses mal formatées
 * - Gestion des adresses qui ne peuvent pas être géocodées automatiquement
 * - Maintien d'un historique des adresses et de leurs coordonnées
 */

class AddressMapping extends Model
{
    protected $fillable = [
        'original_address',
        'clean_address',
        'latitude',
        'longitude'
    ];
}
