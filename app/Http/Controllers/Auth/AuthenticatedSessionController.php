<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Obtener el ID de usuario
        $userId = Auth::guard('web')->id();

        // Enviar mensaje al endpoint
        $client = new \GuzzleHttp\Client();
        $response = $client->put("http://localhost:3001/updateActiveStatus/{$userId}/1");

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Obtener el ID de usuario
        $userId = Auth::guard('web')->id();

        // Cerrar sesión
        Auth::guard('web')->logout();

        // Enviar mensaje al endpoint
        $client = new \GuzzleHttp\Client();
        $response = $client->put("http://localhost:3001/updateActiveStatus/{$userId}/0");

        // Invalidar la sesión y regenerar el token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
