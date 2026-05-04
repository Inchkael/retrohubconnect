<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Commentaire
 *
 * Ce modèle représente un commentaire ou une évaluation laissée par un utilisateur
 * (internaute) pour un prestataire. Il permet de gérer les avis et notes des utilisateurs
 * sur les prestataires de services.
 *
 * Structure de la table :
 * - Utilise une clé primaire personnalisée 'CommentaireID'
 * - Stocke les informations en français (noms des colonnes en français)
 * - Gère les relations avec les utilisateurs (internautes et prestataires)
 *
 * Fonctionnalités principales :
 * - Gestion des commentaires et évaluations des prestataires
 * - Relation avec les utilisateurs (internautes et prestataires)
 * - Stockage des informations sur le commentaire (titre, contenu, note)
 * - Gestion de la date d'encodage du commentaire
 */

class Commentaire extends Model
{
    use HasFactory;

    protected $primaryKey = 'CommentaireID';

    protected $fillable = [
        'Titre',
        'Contenu',
        'Cote',
        'InternauteID',
        'PrestataireID',
    ];

    protected $dates = [
        'Encodage', // pour indiquer que 'Encodage' est une date
    ];

    public function internaute()
    {
        return $this->belongsTo(User::class, 'InternauteID');
    }

    public function prestataire()
    {
        return $this->belongsTo(User::class, 'PrestataireID');
    }
}
