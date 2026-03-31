<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Player\StorePinRequest;
use App\Services\Player\PlayerProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PinController extends Controller
{
    public function __construct(private readonly PlayerProfileService $profileService) {}

    public function store(StorePinRequest $request): RedirectResponse
    {
        $this->profileService->setPin(Auth::guard('player')->user()->player, $request->pin);

        return back();
    }
}
