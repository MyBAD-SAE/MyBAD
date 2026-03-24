<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassParticipantResource;
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
        $user = auth('player')->user();
        $player = $user->player;

        $participation = $player?->selectedParticipation()?->load('participantable.user');
        $classId = $participation?->school_class_id;

        $classes = $player ? $this->dashboardService->getPlayerClasses($player) : [];

        $eloDiff = 0;
        $eloHistory = [];
        $matchStats = ['wins' => 0, 'losses' => 0, 'total' => 0, 'sessions' => []];
        $totalMatches = 0;
        $winStreak = 0;

        if ($player) {
            $matchData = $this->dashboardService->getMatchStats($player, $classId);
            $matchStats = $matchData['matchStats'];
            $totalMatches = $matchData['totalMatches'];

            if ($participation) {
                $eloData = $this->dashboardService->getEloData($participation, $matchStats['total']);
                $eloDiff = $eloData['eloDiff'];
                $eloHistory = $eloData['eloHistory'];
            }

            $winStreak = $this->dashboardService->getWinStreak($player, $matchData['matches']);
        }

        return Inertia::render('Player/Dashboard', [
            'participant'     => $participation ? ClassParticipantResource::make($participation)->resolve() : null,
            'classes'         => $classes,
            'selectedClassId' => $classId,
            'playerCode'      => $player?->code,
            'firstName'       => $user->first_name,
            'avatarUrl'       => $user->profile_picture,
            'eloDiff'         => $eloDiff,
            'eloHistory'      => $eloHistory,
            'matchStats'      => $matchStats,
            'totalMatches'    => $totalMatches,
            'winStreak'       => $winStreak,
            'rankingPlayers'  => $this->rankingService->getRankingForClass($player, $classId),
        ]);
    }
}
