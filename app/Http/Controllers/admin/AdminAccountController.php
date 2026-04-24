<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class AdminAccountController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/account",
     *     tags={"Admin - Compte"},
     *     summary="Page du compte administrateur",
     *     operationId="admin.account.index",
     *     security={{"session":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Props Inertia de la page compte",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="first_name", type="string"),
     *                 @OA\Property(property="last_name", type="string"),
     *                 @OA\Property(property="email", type="string", format="email")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): Response
    {
        /** @var User $user */
        $user = Auth::guard('admin')->user();

        return Inertia::render('Admin/Account', [
            'user' => UserResource::make($user)->resolve(),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/admin/account/profile",
     *     tags={"Admin - Compte"},
     *     summary="Mettre à jour le profil administrateur",
     *     operationId="admin.account.profile",
     *     security={{"session":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email"},
     *             @OA\Property(property="first_name", type="string", maxLength=255),
     *             @OA\Property(property="last_name", type="string", maxLength=255),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255)
     *         )
     *     ),
     *     @OA\Response(response=302, description="Retour avec message de succès"),
     *     @OA\Response(response=422, description="Email déjà utilisé ou données invalides")
     * )
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,' . Auth::guard('admin')->id(),
        ]);

        /** @var User $user */
        $user = Auth::guard('admin')->user();

        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
        ]);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * @OA\Put(
     *     path="/admin/account/password",
     *     tags={"Admin - Compte"},
     *     summary="Changer le mot de passe administrateur",
     *     operationId="admin.account.password",
     *     security={{"session":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password","new_password","new_password_confirmation"},
     *             @OA\Property(property="current_password", type="string", format="password"),
     *             @OA\Property(property="new_password", type="string", format="password", minLength=8),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=302, description="Retour avec message de succès"),
     *     @OA\Response(response=422, description="Mot de passe actuel incorrect")
     * )
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => ['required', Password::min(8), 'confirmed'],
        ], [
            'current_password.required' => 'Le mot de passe actuel est requis.',
            'new_password.required'     => 'Le nouveau mot de passe est requis.',
            'new_password.min'          => 'Le mot de passe doit contenir au moins 8 caractères.',
            'new_password.confirmed'    => 'Les mots de passe ne correspondent pas.',
        ]);

        /** @var User $user */
        $user = Auth::guard('admin')->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }
}
