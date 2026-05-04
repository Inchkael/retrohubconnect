<?php

// app/Http/Middleware/CheckUserLock.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware CheckUserLock
 *
 * Ce middleware vérifie si le compte utilisateur est bloqué en raison de trop de
 * tentatives de connexion infructueuses. Il protège les routes contre les accès
 * des utilisateurs dont le compte a été temporairement verrouillé.
 *
 * Fonctionnement :
 * 1. Vérifie si un utilisateur est connecté (Auth::check())
 * 2. Vérifie si le compte de l'utilisateur est bloqué (Auth::user()->isLocked())
 * 3. Si le compte est bloqué :
 *    - Déconnecte l'utilisateur (Auth::logout())
 *    - Redirige vers la page de connexion avec un message d'erreur
 * 4. Si le compte n'est pas bloqué, laisse passer la requête
 *
 * Utilisation typique :
 * - Appliqué aux routes protégées par authentification
 * - Enregistré dans le kernel et appliqué via le middleware 'check.user.lock'
 * - Protège l'application contre les attaques par force brute
 *
 * Sécurité :
 * - Empêche les utilisateurs bloqués d'accéder aux ressources protégées
 * - Déconnecte automatiquement les utilisateurs bloqués
 * - Fournit un message clair expliquant la raison du blocage
 */

class CheckUserLock
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isLocked()) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Votre compte est temporairement bloqué en raison de trop de tentatives de connexion infructueuses. Veuillez contacter l\'administrateur.');
        }

        return $next($request);
    }
}
