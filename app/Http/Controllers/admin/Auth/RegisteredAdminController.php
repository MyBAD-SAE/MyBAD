<?php

namespace App\Http\Controllers\admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreAdminRequest;
use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisteredAdminController extends Controller
{
    /**
     * @OA\Post(
     *     path="/admin/register",
     *     tags={"Auth Admin"},
     *     summary="Inscription d'un nouvel administrateur",
     *     operationId="admin.register",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email","password","password_confirmation"},
     *             @OA\Property(property="first_name", type="string", maxLength=255, example="Marie"),
     *             @OA\Property(property="last_name", type="string", maxLength=255, example="Dupont"),
     *             @OA\Property(property="email", type="string", format="email", example="marie@example.fr"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8),
     *             @OA\Property(property="password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=302, description="Redirection vers /admin/dashboard"),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        $user = User::create([
            'id' => Str::uuid(),
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        AdminUser::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
        ]);

        Auth::guard('admin')->login($user);

        $request->session()->regenerate();

        return redirect()->intended('/admin/dashboard');
    }
}
