<?php

namespace App\Http\Controllers\player\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StorePlayerRequest;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisteredPlayerController extends Controller
{

    public function store(StorePlayerRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        $user = User::create([
            'id' => Str::uuid(),
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        Player::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'pin' => null,
            'code' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
        ]);
        Auth::guard('player')->login($user);

        return redirect()->intended('/');
    }

}
