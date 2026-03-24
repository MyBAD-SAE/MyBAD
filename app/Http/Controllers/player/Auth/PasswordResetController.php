<?php

namespace App\Http\Controllers\player\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Inertia\Inertia;

class PasswordResetController extends Controller
{
    /**
     * Envoyer le lien de réinitialisation par email.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', true);
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Afficher le formulaire de réinitialisation.
     */
    public function showResetForm(Request $request, string $token)
    {
        return Inertia::render('Player/Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Réinitialiser le mot de passe.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('player.login')->with('status', 'Mot de passe réinitialisé avec succès.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
