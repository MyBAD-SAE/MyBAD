<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Services\Player\EloDetailsService;
use App\Services\Player\PlayerProfileService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class EloDetailsController extends Controller
{
    public function __construct(
        private readonly EloDetailsService $eloDetailsService,
        private readonly PlayerProfileService $profileService,
    ) {}

    public function index(): Response
    {
        $user          = Auth::guard('player')->user();
        $player        = $user->player;
        $participation = $player?->selectedParticipation()?->load('participantable.user');
        $classId       = $participation?->school_class_id;

        return Inertia::render('Player/EloDetails', [
            ...$this->eloDetailsService->getData($participation, $classId),
            'classes'         => $this->profileService->getPlayerClasses($player),
            'selectedClassId' => $classId,
        ]);
    }
}
