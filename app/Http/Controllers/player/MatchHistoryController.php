<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Services\Player\MatchHistoryService;
use App\Services\Player\PlayerProfileService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MatchHistoryController extends Controller
{
    public function __construct(
        private readonly MatchHistoryService $matchHistoryService,
        private readonly PlayerProfileService $profileService,
    ) {}

    public function index(): Response
    {
        $player  = Auth::guard('player')->user()->player;
        $classId = $player->selectedParticipation()?->school_class_id;

        return Inertia::render('Player/MatchHistory', [
            'matches'         => $this->matchHistoryService->getMatches($player, $classId),
            'classes'         => $this->profileService->getPlayerClasses($player),
            'selectedClassId' => $classId,
        ]);
    }
}
