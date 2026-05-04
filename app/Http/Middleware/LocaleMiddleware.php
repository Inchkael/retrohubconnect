<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

/**
 * Middleware LocaleMiddleware
 *
 * Ce middleware est utilisé pour gérer la localisation (langue) de l'application.
 * Il permet de définir la langue de l'application en fonction de la préférence
 * de l'utilisateur stockée dans la session.
 *
 * Fonctionnement :
 * 1. Vérifie si une langue est définie dans la session de l'utilisateur
 * 2. Si une langue est définie, configure l'application pour utiliser cette langue
 * 3. Passe la requête au middleware ou contrôleur suivant
 *
 * Utilisation typique :
 * - Appliqué globalement à toutes les requêtes web via le kernel
 * - Permet aux utilisateurs de choisir leur langue préférée
 * - Maintenir cette préférence entre les requêtes via la session
 *
 * Avantages :
 * - Internationalisation : permet de supporter plusieurs langues
 * - Personnalisation : chaque utilisateur peut choisir sa langue préférée
 * - Persistance : la préférence de langue est maintenue entre les requêtes
 */

class LocaleMiddleware
{
    public function handle($request, Closure $next)
    {
        // Vérifier si la langue est définie dans la session
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }

        return $next($request);
    }
}
