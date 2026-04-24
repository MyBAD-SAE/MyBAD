<?php

namespace App\Http\Controllers\admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Inertia\Inertia;

class AdminPasswordResetController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/forgot-password",
     *     tags={"Auth Admin"},
     *     summary="Afficher le formulaire mot de passe oublié (admin)",
     *     operationId="admin.password.request",
     *     @OA\Response(response=200, description="Page Inertia Admin/Auth/ForgotPassword")
     * )
     */
    public function showForgotForm()
    {
        return Inertia::render('Admin/Auth/ForgotPassword');
    }

    /**
     * @OA\Post(
     *     path="/admin/forgot-password",
     *     tags={"Auth Admin"},
     *     summary="Envoyer le lien de réinitialisation à l'admin",
     *     operationId="admin.password.email",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com")
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
     *     path="/admin/reset-password/{token}",
     *     tags={"Auth Admin"},
     *     summary="Afficher le formulaire de réinitialisation (admin)",
     *     operationId="admin.password.reset",
     *     @OA\Parameter(name="token", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="email", in="query", required=false, @OA\Schema(type="string", format="email")),
     *     @OA\Response(
     *         response=200,
     *         description="Props Inertia du formulaire de réinitialisation admin",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="email", type="string", nullable=true)
     *         )
     *     )
     * )
     */
    public function showResetForm(Request $request, string $token)
    {
        return Inertia::render('Admin/Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/admin/reset-password",
     *     tags={"Auth Admin"},
     *     summary="Réinitialiser le mot de passe de l'admin",
     *     operationId="admin.password.update",
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
     *     @OA\Response(response=302, description="Redirection vers /admin/login après succès"),
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
            return redirect()->route('admin.login')->with('status', 'Mot de passe réinitialisé avec succès.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
