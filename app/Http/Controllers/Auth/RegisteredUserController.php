<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Diviser le champ 'name' en 'last_name' et 'first_name'
        $names = explode(' ', $request->name, 2);
        $lastName = $names[0] ?? '';
        $firstName = $names[1] ?? '';

        $user = User::create([
            'last_name' => $lastName,
            'first_name' => $firstName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'member', // Rôle par défaut
            'language' => 'fr', // Langue par défaut
            'enabled' => true, // Compte activé par défaut
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
