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
     * @OA\Get(
     *     path="/player/login",
     *     tags={"Auth Joueur"},
     *     summary="Page de connexion joueur",
     *     operationId="player.login.form",
     *     @OA\Response(response=200, description="Page de connexion rendue par Inertia")
     * )
     */
    public function create(): Response
    {
        return Inertia::render('Player/Auth/Auth', [
            'status' => session('status'),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/player/login",
     *     tags={"Auth Joueur"},
     *     summary="Connexion joueur",
     *     operationId="player.login.submit",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="joueur@example.fr"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=302, description="Redirection vers /"),
     *     @OA\Response(response=422, description="Identifiants incorrects ou compte non associé à un joueur")
     * )
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
     * @OA\Post(
     *     path="/joueur/profil/logout",
     *     tags={"Auth Joueur"},
     *     summary="Déconnexion joueur",
     *     operationId="player.logout",
     *     security={{"session":{}}},
     *     @OA\Response(response=302, description="Redirection vers /player/login")
     * )
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('player')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('player.login');
    }
}
