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
    public function index(): Response
    {
        /** @var User $user */
        $user = Auth::guard('admin')->user();

        return Inertia::render('Admin/Account', [
            'user' => UserResource::make($user)->resolve(),
        ]);
    }

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
