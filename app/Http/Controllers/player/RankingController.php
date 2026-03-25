<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;
use App\Services\Ranking\RankingService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RankingController extends Controller
{
    public function __construct(
        private readonly RankingService $rankingService,
        private readonly DashboardService $dashboardService,
    ) {}

    public function index(): Response
    {
        $user = Auth::guard('player')->user();
        $player = $user->player;
        $classId = $player?->selectedParticipation()?->school_class_id;

        $classes = $player ? $this->dashboardService->getPlayerClasses($player) : [];

        return Inertia::render('Player/Rankings', [
            'players'         => $this->rankingService->getRankingForClass($player, $classId),
            'classes'         => $classes,
            'selectedClassId' => $classId,
        ]);
    }
}
