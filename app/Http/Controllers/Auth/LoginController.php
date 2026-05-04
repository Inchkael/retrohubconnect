<?php
/**
 * LoginController.php
 *
 * Contrôleur pour gérer l'authentification des utilisateurs.
 * Utilise Laravel Socialite pour l'authentification OAuth (Google)
 * et une logique personnalisée pour les identifiants classiques (email/téléphone + mot de passe).
 * Implémente un système de blocage de compte après 4 tentatives de connexion infructueuses.
 *
 * @author Mickaël Collings (Espace de Mickaël Collings)
 * @version 1.5
 * @created 2026-01-20
 * @updated 2026-03-25 (ajout système de blocage après 4 tentatives)
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Display the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle a custom login request (email/phone + password).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customLogin(Request $request)
    {
        $credentials = $request->validate([
            'email_or_phone' => ['required', 'string'],
            'password'       => ['required', 'string'],
        ]);

        $loginType = filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile_phone';
        $user = User::where($loginType, $request->email_or_phone)->first();

        // Vérifier si l'utilisateur existe et est bloqué
        if ($user && $user->isLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte est bloqué après 4 tentatives de connexion infructueuses. Veuillez contacter l\'administrateur.'
            ], 403);
        }

        if ($user && Hash::check($request->password, $user->password)) {
            if (!$user->is_confirmed) {
                return response()->json([
                    'success' => false,
                    'needs_finalization' => true,
                    'token' => $user->confirmation_token,
                    'message' => 'Veuillez compléter votre inscription avant de vous connecter.'
                ]);
            }

            // Réinitialiser les tentatives en cas de succès
            $user->resetLoginAttempts();

            Auth::login($user, $request->filled('remember'));
            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie !',
                'redirect' => route('home')
            ]);
        }

        // Incrémenter les tentatives en cas d'échec
        if ($user) {
            $user->incrementLoginAttempts();
            Log::warning("Tentative de connexion échouée pour l'utilisateur ID: {$user->id}. Tentatives: {$user->login_attempts}/4");
        }

        return response()->json([
            'success' => false,
            'message' => 'Identifiants incorrects.'
        ], 401);
    }

    /**
     * Handle a classic login request (email + password).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        // Vérifier si l'utilisateur existe et est bloqué
        if ($user && $user->isLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte est bloqué après 4 tentatives de connexion infructueuses. Veuillez contacter l\'administrateur.'
            ], 403);
        }

        if ($user && Hash::check($request->password, $user->password)) {
            if (!$user->is_confirmed) {
                return response()->json([
                    'success' => false,
                    'needs_finalization' => true,
                    'token' => $user->confirmation_token,
                    'message' => 'Veuillez compléter votre inscription avant de vous connecter.',
                ], 200);
            }

            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();

                // Réinitialiser les tentatives en cas de succès
                $user->resetLoginAttempts();

                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie !',
                    'redirect' => route('home')
                ]);
            }
        }

        // Incrémenter les tentatives en cas d'échec
        if ($user) {
            $user->incrementLoginAttempts();
            Log::warning("Tentative de connexion échouée pour l'utilisateur ID: {$user->id}. Tentatives: {$user->login_attempts}/4");
        }

        return response()->json([
            'success' => false,
            'message' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ], 401);
    }

    /**
     * Handle the Google callback after authentication.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'first_name' => explode(' ', $googleUser->name)[0] ?? 'Unknown',
                    'last_name'  => explode(' ', $googleUser->name)[1] ?? '',
                    'email'      => $googleUser->email,
                    'password'   => bcrypt(Str::random(24)),
                    'role'       => 'USER',
                    'is_confirmed' => false,
                    'confirmation_token' => Str::random(60),
                ]);
            }

            // Vérifie si l'utilisateur est bloqué
            if ($user->isLocked()) {
                return redirect()->route('login')
                    ->with('error', 'Votre compte est bloqué après 4 tentatives de connexion infructueuses. Veuillez contacter l\'administrateur.');
            }

            // Vérifie si l'utilisateur doit finaliser son inscription
            if (!$user->is_confirmed) {
                Auth::login($user, true);
                return redirect()->route('complete.registration', ['token' => $user->confirmation_token]);
            }

            // Réinitialiser les tentatives en cas de succès
            $user->resetLoginAttempts();

            Auth::login($user, true);
            return redirect()->route('home')->with('success', 'Connexion avec Google réussie !');

        } catch (Exception $e) {
            Log::error("Erreur lors de la connexion Google: " . $e->getMessage());
            return redirect()->route('login')->with('error', 'Échec de la connexion avec Google.');
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
