<?php

namespace App\Http\Controllers\admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdminAuthenticatedSessionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/login",
     *     tags={"Auth Admin"},
     *     summary="Page de connexion administrateur",
     *     operationId="admin.login.form",
     *     @OA\Response(response=200, description="Page de connexion rendue par Inertia")
     * )
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Auth/Auth', [
            'status' => session('status'),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/admin/login",
     *     tags={"Auth Admin"},
     *     summary="Connexion administrateur",
     *     operationId="admin.login.submit",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.fr"),
     *             @OA\Property(property="password", type="string", format="password", example="motdepasse123")
     *         )
     *     ),
     *     @OA\Response(response=302, description="Redirection vers /admin/dashboard"),
     *     @OA\Response(response=422, description="Identifiants incorrects ou compte non-admin")
     * )
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate('admin');

        $user = Auth::guard('admin')->user();

        if (! $user->adminUser) {
            Auth::guard('admin')->logout();

            throw ValidationException::withMessages([
                'email' => 'Identifiants incorrects.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended('/admin/dashboard');
    }

    /**
     * @OA\Post(
     *     path="/admin/logout",
     *     tags={"Auth Admin"},
     *     summary="Déconnexion administrateur",
     *     operationId="admin.logout",
     *     security={{"session":{}}},
     *     @OA\Response(response=302, description="Redirection vers /admin/login")
     * )
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
