<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;
use App\Services\Ranking\RankingService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly RankingService $rankingService,
    ) {}

    public function index(): Response
    {
        $user    = auth('player')->user();
        $player  = $user->player;
        $classId = $player?->selectedParticipation()?->school_class_id;

        return Inertia::render('Player/Dashboard', [
            ...$this->dashboardService->getDashboardData($user),
            'rankingPlayers' => $this->rankingService->getRankingForClass($player, $classId),
        ]);
    }
}
