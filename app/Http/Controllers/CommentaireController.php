<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôleur CommentaireController
 *
 * Ce contrôleur gère les opérations liées aux commentaires laissés par les utilisateurs
 * sur les prestataires. Il permet notamment d'enregistrer de nouveaux commentaires
 * avec validation des données et association à l'utilisateur connecté.
 *
 * Fonctionnalités principales :
 * - Création de nouveaux commentaires
 * - Validation des données de commentaire
 * - Association des commentaires aux utilisateurs et prestataires
 * - Gestion des notes (cotes) attribuées aux prestataires
 */

class CommentaireController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'Titre' => 'required|string|max:255',
            'Contenu' => 'required|string',
            'Cote' => 'required|integer|min:0|max:5',
            'PrestataireID' => 'required|exists:users,id',
        ]);

        $commentaire = new Commentaire();
        $commentaire->Titre = $request->Titre;
        $commentaire->Contenu = $request->Contenu;
        $commentaire->Cote = $request->Cote;
        $commentaire->InternauteID = Auth::id();
        $commentaire->PrestataireID = $request->PrestataireID;
        $commentaire->save();

        return redirect()->back()->with('success', 'Votre commentaire a été enregistré avec succès.');
    }
}
