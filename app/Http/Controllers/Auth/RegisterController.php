<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Notifications\EmailConfirmationNotification;
use Illuminate\Support\Str;

/**
 * Contrôleur RegisterController
 *
 * Ce contrôleur gère le processus d'inscription des utilisateurs, y compris :
 * - L'inscription initiale
 * - La confirmation par email
 * - La finalisation du profil
 *
 * Fonctionnalités principales :
 * - Inscription des nouveaux utilisateurs
 * - Confirmation des adresses email via un token
 * - Finalisation du profil avec des informations supplémentaires
 * - Gestion des erreurs et journalisation
 * - Mode bypass pour le développement
 */

class RegisterController extends Controller
{
    /**
     * Handle a registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        Log::info('Registration attempt:', $request->all());

        $validator = Validator::make($request->all(), [
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation errors:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'message' => 'Veuillez corriger les erreurs dans le formulaire.'
            ], 422);
        }

        try {
            $user = User::create([
                'first_name'       => $request->first_name,
                'last_name'        => $request->last_name,
                'email'            => $request->email,
                'password'         => Hash::make($request->password),
                'role'             => 'USER',
                'is_confirmed'     => false,
                'registration_date'=> now(),
            ]);

            // Génère un token de confirmation
            $user->generateConfirmationToken();

            // Si le mode bypass est désactivé, envoie l'email de confirmation
            if (!config('mail.bypass_sending')) {
                $user->notify(new EmailConfirmationNotification());
                Log::info('Email de confirmation envoyé à : ' . $user->email);
            } else {
                // Simule la confirmation de l'email
                $user->confirmEmail();
                Log::info('Email de confirmation simulé pour : ' . $user->email . ' (mode bypass activé)');
            }

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie ! ' . (config('mail.bypass_sending') ? 'Votre compte a été confirmé automatiquement.' : 'Veuillez vérifier votre email pour valider votre compte.'),
                'redirect' => route('login')
            ]);

        } catch (\Exception $e) {
            Log::error('Registration failed:', [
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
    /**
     * Confirme l'email de l'utilisateur via le token.
     */
    /**
     * Confirme l'email de l'utilisateur via le token et redirige vers la finalisation.
     */
    public function confirmEmail($token)
    {
        $user = User::where('confirmation_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token de confirmation invalide.');
        }

        // Ne confirme pas encore l'utilisateur, mais vérifie que le token est valide
        if ($user->is_confirmed) {
            return redirect()->route('login')->with('error', 'Ce compte est déjà confirmé. Veuillez vous connecter.');
        }

        // Redirige vers la page de finalisation d'inscription avec le token
        return redirect()->route('complete.registration', ['token' => $token]);
    }
    public function showCompleteRegistrationForm(Request $request)
    {
        $token = $request->query('token'); // Récupère le token depuis l'URL (?token=...)
        $user = User::where('confirmation_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token de confirmation invalide.');
        }

        if ($user->is_confirmed) {
            return redirect()->route('login')->with('error', 'Ce compte est déjà confirmé. Veuillez vous connecter.');
        }

        return view('auth.complete-registration', [
            'token' => $token,
            'user' => $user
        ]);
    }


    /**
     * Traite la complétion des informations utilisateur.
     */
    /**
     * Traite la complétion des informations utilisateur.
     */
    public function completeRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

            // Met à jour les informations et finalise l'inscription
            $user->update([
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'address'       => $request->address,
                'mobile_phone'  => $request->mobile_phone,
                'vat_number'    => $request->vat_number,
                'role'          => $request->role,
                'is_confirmed'  => true,
                'confirmed_at'  => now(),
                'email_verified_at' => now(), // Confirme l'email
                'confirmation_token' => null, // Supprime le token après confirmation
            ]);

            // Connecte l'utilisateur après finalisation
            Auth::login($user);

            return response()->json([
                'success' => true,
                'message' => 'Votre inscription a été complétée avec succès !',
                'redirect' => route('home')
            ]);

        } catch (\Exception $e) {
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
