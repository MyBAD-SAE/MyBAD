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
