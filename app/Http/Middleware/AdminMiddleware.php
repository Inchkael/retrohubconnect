<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'utilisateur est connecté et s'il a le rôle 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Redirige ou retourne une erreur si l'utilisateur n'est pas admin
        abort(403, 'Accès interdit. Vous devez être administrateur pour accéder à cette page.');
    }
}
