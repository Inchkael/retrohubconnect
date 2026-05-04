<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Notifications\EmailConfirmationNotification;

/**
 * Contrôleur GoogleAuthController
 *
 * Ce contrôleur gère l'authentification via Google OAuth.
 * Il permet aux utilisateurs de se connecter ou de s'inscrire en utilisant leur compte Google.
 * Le processus inclut la création de compte pour les nouveaux utilisateurs,
 * la confirmation par email et la finalisation du profil.
 *
 * Fonctionnalités principales :
 * - Redirection vers la page d'authentification Google
 * - Gestion du callback après authentification Google
 * - Création de nouveaux utilisateurs via Google
 * - Finalisation du profil utilisateur
 * - Confirmation par email
 */

class GoogleAuthController extends Controller
{
    /**
     * Redirige vers la page d'authentification Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Gère le callback Google après authentification.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->email)->first();

            // Crée l'utilisateur s'il n'existe pas
            if (!$user) {
                $user = User::create([
                    'first_name'       => explode(' ', $googleUser->name)[0] ?? 'Unknown',
                    'last_name'        => explode(' ', $googleUser->name)[1] ?? '',
                    'email'            => $googleUser->email,
                    'password'         => bcrypt(Str::random(24)),
                    'role'             => 'USER',
                    'is_confirmed'     => false,
                    'google_id'        => $googleUser->id,
                    'avatar'           => $googleUser->avatar ?? null, // Gère le cas où avatar n'existe pas
                    'confirmation_token' => Str::random(60),
                    'registration_date' => now(),
                ]);
                Log::info("Nouvel utilisateur Google créé (non confirmé) : " . $user->email);
            }

            // Si l'utilisateur n'est pas confirmé, envoie un email de confirmation
            if (!$user->is_confirmed) {
                $user->generateConfirmationToken();
                $user->notify(new EmailConfirmationNotification());

                // Retourne une réponse JSON pour le message popup
                return response()->json([
                    'success' => true,
                    'message' => 'Inscription réussie ! Veuillez vérifier votre email pour valider votre compte.',
                    'redirect' => route('login')
                ]);
            }

            // Si l'utilisateur est déjà confirmé, connecte-le
            Auth::login($user, true);
            return redirect()->route('home')
                ->with('success', 'Connexion réussie ! Bienvenue, ' . $user->first_name);

        } catch (Exception $e) {
            Log::error("Erreur GoogleAuthCallback : " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Échec de la connexion avec Google. Veuillez réessayer.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche le formulaire de complétion d'inscription.
     */
    public function showCompleteRegistrationForm($token)
    {
        $user = User::where('confirmation_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token de confirmation invalide.');
        }

        if ($user->is_confirmed) {
            return redirect()->route('login')->with('error', 'Ce compte est déjà confirmé. Veuillez vous connecter.');
        }

        return view('auth.complete-registration', [
            'token' => $token,
            'user'  => $user
        ]);
    }

    /**
     * Traite la complétion des informations utilisateur.
     */
    public function completeRegistration(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'token'         => 'required|string',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'address'       => 'required|string|max:255',
            'mobile_phone'  => 'required|string|max:20',
            'vat_number'    => 'nullable|string|max:20',
            'role'          => 'required|in:USER,PROVIDER',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'message' => 'Veuillez corriger les erreurs dans le formulaire.'
            ], 422);
        }

        try {
            $user = User::where('confirmation_token', $request->token)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token de confirmation invalide.'
                ], 404);
            }

            if ($user->is_confirmed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce compte est déjà confirmé. Veuillez vous connecter.'
                ], 400);
            }

            $user->update([
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'address'       => $request->address,
                'mobile_phone'  => $request->mobile_phone,
                'vat_number'    => $request->vat_number,
                'role'          => $request->role,
                'is_confirmed'  => true,
                'confirmed_at'  => now(),
                'confirmation_token' => null,
            ]);

            Auth::login($user);
            return response()->json([
                'success' => true,
                'message' => 'Votre inscription a été complétée avec succès !',
                'redirect' => route('home')
            ]);

        } catch (Exception $e) {
            Log::error('Complete registration failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage(),
                'code'    => $e->getCode()
            ], 500);
        }
    }
}
