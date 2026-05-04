<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Promotion
 *
 * Ce modèle représente une promotion dans le système. Une promotion est une offre spéciale
 * proposée par un prestataire pour une catégorie de services spécifique, pendant une période limitée.
 *
 * Fonctionnalités principales :
 * - Gestion des promotions proposées par les prestataires
 * - Relation avec les catégories de services
 * - Relation avec les prestataires (utilisateurs)
 * - Stockage des informations sur la période de validité et d'affichage
 * - Gestion des documents PDF associés aux promotions
 *
 * Structure de la table :
 * - Utilise une table dédiée 'promotions' avec une clé primaire personnalisée 'PromotionID'
 * - Stocke les informations en français (noms des colonnes en français)
 */

class Promotion extends Model
{
    use HasFactory;

    protected $table = 'promotions';
    protected $primaryKey = 'PromotionID';

    protected $fillable = [
        'Nom',
        'Description',
        'DocumentPdf',
        'Début',
        'Fin',
        'AffichageDébut',
        'AffichageFin',
        'CategorieDeServicesID',
        'PrestataireID',
    ];

    public function categorieDeServices()
    {
        return $this->belongsTo(ServiceCategory::class, 'CategorieDeServicesID', 'id');
    }

    public function prestataire()
    {
        return $this->belongsTo(User::class, 'PrestataireID', 'id');
    }
}
