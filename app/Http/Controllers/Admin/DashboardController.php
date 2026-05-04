<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;

/**
 * Contrôleur DashboardController
 *
 * Ce contrôleur gère l'affichage du tableau de bord de l'interface d'administration.
 * Il fournit les données nécessaires pour afficher les informations de base
 * dans le tableau de bord administratif.
 *
 * Fonctionnalités principales :
 * - Affichage du tableau de bord administratif
 * - Récupération des données nécessaires pour le dashboard
 * - Préparation des données pour la vue
 */

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord administration.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupère les catégories de services triées par nom
        $categories = ServiceCategory::orderBy('name')->get();

        // Retourne la vue du dashboard avec les catégories
        return view('admin.dashboard', compact('categories'));
    }
}
