<?php

namespace App\Http\Controllers\player\Auth;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleAuthController extends Controller
{
    /**
     * @OA\Get(
     *     path="/player/auth/google/redirect",
     *     tags={"Auth Joueur"},
     *     summary="Redirection vers Google OAuth",
     *     operationId="player.google.redirect",
     *     @OA\Response(response=302, description="Redirection vers l'URL d'autorisation Google")
     * )
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * @OA\Get(
     *     path="/player/auth/google/callback",
     *     tags={"Auth Joueur"},
     *     summary="Callback Google OAuth — connexion ou création du joueur",
     *     operationId="player.google.callback",
     *     @OA\Parameter(name="code", in="query", required=false, description="Code OAuth2 Google", @OA\Schema(type="string")),
     *     @OA\Parameter(name="error", in="query", required=false, description="Erreur retournée par Google", @OA\Schema(type="string")),
     *     @OA\Response(response=302, description="Redirection vers / (succès) ou /player/login (erreur ou annulation)")
     * )
     */
    public function callback(): RedirectResponse
    {
        if (request()->has('error')) {
            return redirect()->route('player.login')->withErrors([
                'email' => 'Connexion Google annulée.',
            ]);
        }
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException) {
            return redirect()->route('player.login')->withErrors([
                'email' => 'La session a expiré, veuillez réessayer.',
            ]);
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update(['google_id' => $googleUser->getId()]);
            } else {
                $user = User::create([
                    'id' => Str::uuid(),
                    'first_name' => $googleUser->user['given_name'] ?? $googleUser->getName(),
                    'last_name' => $googleUser->user['family_name'] ?? '',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'profile_picture' => $googleUser->getAvatar(),
                ]);

                Player::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'code' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                ]);
            }
        }

        if (! $user->player) {
            return redirect()->route('player.login')->withErrors([
                'email' => 'Ce compte n\'est pas associé à un joueur.',
            ]);
        }

        Auth::guard('player')->login($user);

        request()->session()->regenerate();

        return redirect()->intended('/');
    }
}
