<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        if (request()->has('error')) {
            return redirect()->route('player.login')->withErrors([
                'email' => 'Connexion Google annulée.',
            ]);
        }
        $googleUser = Socialite::driver('google')->user();

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
