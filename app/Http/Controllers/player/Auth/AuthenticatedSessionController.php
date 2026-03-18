<?php

namespace App\Http\Controllers\player\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate('player');

        $user = Auth::guard('player')->user();

        if (! $user->player) {
            Auth::guard('player')->logout();

            throw ValidationException::withMessages([
                'email' => 'Ce compte n\'est pas associé à un joueur.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('player')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('player.login');
    }
}
