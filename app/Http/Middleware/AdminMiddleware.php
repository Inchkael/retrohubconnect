<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware AdminMiddleware
 *
 * Ce middleware est utilisé pour restreindre l'accès à certaines routes
 * uniquement aux utilisateurs ayant le rôle "ADMIN" (administrateur).
 *
 * Fonctionnement :
 * 1. Vérifie si l'utilisateur est connecté (auth()->check())
 * 2. Vérifie si l'utilisateur a le rôle "ADMIN" (auth()->user()->isAdmin())
 * 3. Si les deux conditions sont remplies, l'utilisateur peut accéder à la route
 * 4. Sinon, une erreur 403 (Accès interdit) est retournée avec un message explicite
 *
 * Utilisation typique :
 * - Protège les routes spécifiques aux administrateurs
 * - Appliqué via le kernel ou directement dans les définitions de routes
 * - Exemple: Route::middleware(['auth', 'admin'])->group(...)
 *
 * Avantages :
 * - Sécurité : restreint l'accès aux fonctionnalités réservées aux administrateurs
 * - Simplicité : logique de vérification centralisée dans un seul endroit
 * - Réutilisabilité : peut être appliqué à plusieurs routes
 * - Clarté : message d'erreur explicite pour les utilisateurs non autorisés
 */

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'utilisateur est connecté et a le rôle "ADMIN"
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // Redirige ou retourne une erreur 403 si l'utilisateur n'est pas admin
        abort(403, 'Accès interdit : vous devez être administrateur.');
    }


}
