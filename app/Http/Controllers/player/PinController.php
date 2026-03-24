<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Player\StorePinRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PinController extends Controller
{
    public function store(StorePinRequest $request): RedirectResponse
    {
        Auth::guard('player')->user()->player->update(['pin' => $request->pin]);

        return back();
    }
}
