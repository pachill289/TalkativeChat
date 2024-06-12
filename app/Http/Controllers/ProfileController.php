<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $userId = $request->user()->id;
        $response = Http::get("http://localhost:3001/getUserConfig/{$userId}");

        $avatarUrl = null;
        if ($response->ok()) {
            $avatarUrl = $response->json()['avatar'];
        }

        return view('profile.edit', [
            'user' => $request->user(),
            'avatarUrl' => $avatarUrl,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }


        $user = $request->user();

        if ($request->has('avatar_url')) {
            $avatarUrl = $request->input('avatar_url');

            // Actualiza el avatar en la base de datos de Firebase
            $firebaseResponse = Http::put('http://localhost:3001/updateAvatar/' . $user->id, [
                'avatar' => $avatarUrl,
            ]);

            if ($firebaseResponse->failed()) {
                return back()->withErrors(['avatar' => 'Error al actualizar el avatar en Firebase']);
            }
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
