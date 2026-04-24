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
     * @OA\Post(
     *     path="/player/auth/mot-de-passe-oublie",
     *     tags={"Auth Joueur"},
     *     summary="Envoyer le lien de réinitialisation de mot de passe",
     *     operationId="player.password.email",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="joueur@example.com")
     *         )
     *     ),
     *     @OA\Response(response=302, description="Redirection avec message de succès"),
     *     @OA\Response(response=422, description="Email invalide ou introuvable")
     * )
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
     * @OA\Get(
     *     path="/player/auth/reset-password/{token}",
     *     tags={"Auth Joueur"},
     *     summary="Afficher le formulaire de réinitialisation de mot de passe",
     *     operationId="player.password.reset",
     *     @OA\Parameter(name="token", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="email", in="query", required=false, @OA\Schema(type="string", format="email")),
     *     @OA\Response(
     *         response=200,
     *         description="Props Inertia du formulaire de réinitialisation",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="email", type="string", nullable=true)
     *         )
     *     )
     * )
     */
    public function showResetForm(Request $request, string $token)
    {
        return Inertia::render('Player/Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/player/auth/reset-password",
     *     tags={"Auth Joueur"},
     *     summary="Réinitialiser le mot de passe du joueur",
     *     operationId="player.password.update",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token","email","password","password_confirmation"},
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", minLength=8),
     *             @OA\Property(property="password_confirmation", type="string")
     *         )
     *     ),
     *     @OA\Response(response=302, description="Redirection vers /player/login après succès"),
     *     @OA\Response(response=422, description="Token invalide ou email incorrect")
     * )
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
