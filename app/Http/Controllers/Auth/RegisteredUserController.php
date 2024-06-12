<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

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
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'avatar_url' => ['required', 'url'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => $request->avatar_url,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Hacer la solicitud HTTP POST al endpoint
        $response = Http::post('http://localhost:3001/addUser', [
            'id_user' => $user->id,
            'active_status' => true,
            'avatar' => $request->avatar_url,
        ]);

        if ($response->failed()) {
            // Manejar el error si la solicitud falló
            return redirect()->back()->withErrors('Error al crear el usuario en el sistema externo.');
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
