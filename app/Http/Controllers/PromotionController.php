<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Contrôleur PromotionController
 *
 * Ce contrôleur gère les opérations liées aux promotions proposées par les prestataires.
 * Il permet de créer et supprimer des promotions, avec validation des données et gestion
 * des fichiers PDF associés.
 *
 * Fonctionnalités principales :
 * - Création de nouvelles promotions
 * - Suppression de promotions existantes
 * - Validation des données de promotion
 * - Gestion des fichiers PDF associés
 * - Vérification des autorisations
 */

class PromotionController extends Controller
{
    public function create()
    {
        $categories = ServiceCategory::where('is_validated', true)->get();
        return view('promotions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Nom' => 'required|string|max:255',
            'Description' => 'required|string',
            'CategorieDeServicesID' => 'required|exists:service_categories,id',
            'Début' => 'required|date',
            'Fin' => 'required|date|after:Début',
            'AffichageDébut' => 'required|date',
            'AffichageFin' => 'required|date|after:AffichageDébut',
            'pdf' => 'required|file|mimes:pdf|max:2048',
        ]);

        $promotion = Promotion::create([
            ...$validated,
            'PrestataireID' => Auth::id(),
        ]);

        if ($request->hasFile('pdf')) {
            $path = $request->file('pdf')->store('promotions', 'public');
            $promotion->DocumentPdf = $path;
            $promotion->save();
        }

        return redirect()->route('providers.edit')->with('success', 'Promotion ajoutée avec succès.');
    }

    public function destroy(Promotion $promotion)
    {
        $this->authorize('delete', $promotion);
        Storage::delete('public/' . $promotion->DocumentPdf);
        $promotion->delete();
        return redirect()->back()->with('success', 'Promotion supprimée avec succès.');
    }
}
