<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;

/**
 * Contrôleur ForgotPasswordController
 *
 * Ce contrôleur gère le processus de réinitialisation du mot de passe, y compris :
 * - L'envoi du lien de réinitialisation
 * - L'affichage du formulaire de réinitialisation
 * - La réinitialisation effective du mot de passe
 *
 * Fonctionnalités principales :
 * - Envoi d'un email avec un lien de réinitialisation
 * - Validation des tokens de réinitialisation
 * - Réinitialisation sécurisée des mots de passe
 * - Mode bypass pour le développement
 */

class ForgotPasswordController extends Controller
{
    /**
     * Handle a forgot password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun utilisateur trouvé avec cette adresse email.',
            ], 404);
        }

        $token = Str::random(60);
        $user->password_reset_token = $token;
        $user->token_created_at = now(); // Ajoute cette ligne
        $user->save();

        // Si le mode bypass est désactivé, envoyer l'email
        if (!config('mail.bypass_sending')) {
            Mail::to($user->email)->send(new PasswordResetMail($user, $token));
        } else {
            // Simuler l'envoi (optionnel : stocker le token en base)
            Log::info("Email de réinitialisation simulé pour {$user->email} (mode bypass activé). Token : $token");
        }

        return response()->json([
            'success' => true,
            'message' => 'Un lien de réinitialisation a été envoyé à votre adresse email.',
        ]);
    }


    /**
     * Handle the password reset request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)
            ->where('password_reset_token', $request->token)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token de réinitialisation invalide.',
            ], 404);
        }

        // Mettre à jour le mot de passe
        $user->password = Hash::make($request->password);
        $user->password_reset_token = null;
        $user->token_created_at = null; // Réinitialiser également token_created_at
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Votre mot de passe a été réinitialisé avec succès !',
        ]);
    }
    /**
     * Display the password reset form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }


}
