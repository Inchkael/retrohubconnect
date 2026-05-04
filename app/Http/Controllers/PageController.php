<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Contrôleur PageController
 *
 * Ce contrôleur est responsable de la gestion des pages statiques de l'application,
 * notamment les pages "À propos" et "Contact". Il suit le principe de séparation des
 * responsabilités en isolant la logique des pages statiques dans un contrôleur dédié.
 *
 * Fonctionnalités principales :
 * - Gestion des pages statiques (À propos, Contact)
 * - Préparation des données pour les vues
 * - Personnalisation des titres de page
 * - Structure modulaire pour une maintenance facile
 *
 * Ce contrôleur est particulièrement utile pour :
 * - Centraliser la logique des pages statiques
 * - Faciliter les mises à jour du contenu statique
 * - Maintenir une séparation claire entre la logique et la présentation
 */
class PageController extends Controller
{
    /**
     * Affiche la page "À propos".
     *
     * @return \Illuminate\View\View
     */
    public function about()
    {
        // Données dynamiques pour la vue (exemple : équipe, histoire)
        $teamMembers = [
            [
                'name' => 'Mickael Collings',
                'role' => 'Fondateur & Développeur',
                'bio' => 'Expert en design graphique (IFAPME) et développement web (Institut Saint-Laurent, Liège).',
                'photo' => 'https://via.placeholder.com/150'
            ],
            // Ajoute d'autres membres ici
        ];

        return view('about', [
            'title' => 'À propos | Espace Bien-Être',
            'team' => $teamMembers
        ]);
    }

    /**
     * Affiche la page "Contact".
     *
     * @return \Illuminate\View\View
     */
    public function contact()
    {
        return view('contact', [
            'title' => 'Contact | Espace Bien-Être'
        ]);
    }

    /**
     * Affiche la page du Marketplace.
     *
     * @return \Illuminate\View\View
     */
    public function marketplace()
    {
        return view('retrohubconnect.marketplace', [
            'title' => 'Marketplace | RetroHubConnect'
        ]);
    }

    /**
     * Affiche la page des Forums.
     *
     * @return \Illuminate\View\View
     */
    public function forums()
    {
        return view('retrohubconnect.forums', [
            'title' => 'Forums | RetroHubConnect'
        ]);
    }

    /**
     * Affiche la page des Services.
     *
     * @return \Illuminate\View\View
     */
    public function services()
    {
        return view('retrohubconnect.services', [
            'title' => 'Services | RetroHubConnect'
        ]);
    }

    /**
     * Affiche les résultats de recherche.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        // Logique de recherche ici (ex: recherche d'annonces, de sujets de forum, etc.)
        $query = $request->input('query', '');

        return view('retrohubconnect.search', [
            'title' => 'Recherche | RetroHubConnect',
            'query' => $query
        ]);
    }



}
