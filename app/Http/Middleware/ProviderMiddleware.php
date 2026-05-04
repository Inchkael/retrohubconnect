<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware ProviderMiddleware
 *
 * Ce middleware est utilisé pour restreindre l'accès à certaines routes
 * uniquement aux utilisateurs ayant le rôle "PROVIDER" (prestataire).
 *
 * Fonctionnement :
 * 1. Vérifie si l'utilisateur est connecté (auth()->check())
 * 2. Vérifie si l'utilisateur a le rôle "PROVIDER" (auth()->user()->isProvider())
 * 3. Si les deux conditions sont remplies, l'utilisateur peut accéder à la route
 * 4. Sinon, une erreur 403 (Accès interdit) est retournée
 *
 * Utilisation typique :
 * - Protége les routes spécifiques aux prestataires
 * - Appliqué via le kernel ou directement dans les définitions de routes
 * - Exemple: Route::middleware(['auth', 'provider'])->group(...)
 *
 * Avantages :
 * - Sécurité : restreint l'accès aux fonctionnalités réservées aux prestataires
 * - Simplicité : logique de vérification centralisée dans un seul endroit
 * - Réutilisabilité : peut être appliqué à plusieurs routes
 */

class ProviderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'utilisateur est connecté et a le rôle "PROVIDER"
        if (auth()->check() && auth()->user()->isProvider()) {
            return $next($request);
        }

        // Redirige ou retourne une erreur 403 si l'utilisateur n'est pas un prestataire
        abort(403, 'Accès interdit : vous devez être un prestataire.');
    }
}
