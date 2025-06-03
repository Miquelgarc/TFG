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
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:' . User::class,
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'password_confirmation' => 'required|string',
        ], [
            'email.unique' => 'Aquest correu electrònic ja està registrat.',
            'name.unique' => 'Aquest nom d\'usuari ja està en ús.',
            'password.confirmed' => 'Les contrasenyes no coincideixen.',
            'password.required' => 'La contrasenya és obligatòria.',
            'password_confirmation.required' => 'La confirmació de la contrasenya és obligatòria.',
            'name.required' => 'El nom d\'usuari és obligatori.',
            'email.required' => 'El correu electrònic és obligatori.',
            'email.email' => 'El correu electrònic ha de ser vàlid.',
            'email.lowercase' => 'El correu electrònic ha de ser en minúscules.',
            'password.min' => 'La contrasenya ha de tenir com a mínim 8 caràcters.',
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 2, // Asignar el rol de "afiliat" 
            'status' => 'pending', // Establecer el estado como "pending"
        ]);

        event(new Registered($user));

        Auth::login($user);

        return to_route('info-afiliat')
            ->with('status', 'Registre completat. La vostra sol·licitud d\'afiliació està pendent de revisió.');
    }
}
