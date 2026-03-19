<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PinController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'pin' => ['required', 'string', 'size:4', 'regex:/^\d{4}$/'],
        ]);

        /** @var User $user */
        $user = Auth::guard('player')->user();
        $user->player->update(['pin' => $request->pin]);

        return back();
    }
}
