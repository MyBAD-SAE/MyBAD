<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameMatchResource;
use App\Models\EloHistory;
use App\Models\GameMatch;
use App\Services\Dashboard\DashboardService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MatchHistoryController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    public function index(): Response
    {
        $player        = Auth::guard('player')->user()->player;
        $participation = $player->selectedParticipation();
        $classId       = $participation?->school_class_id;

        $classes = $player ? $this->dashboardService->getPlayerClasses($player) : [];

        $eloHistoriesByMatchId = $participation
            ? EloHistory::where('participant_id', $participation->id)
                ->whereNotNull('game_match_id')
                ->get()
                ->keyBy('game_match_id')
            : collect();

        $matches = GameMatch::forPlayer($player->id)
            ->when($classId, fn ($q) => $q->forClass($classId))
            ->with(['players.user'])
            ->latest()
            ->get()
            ->map(fn (GameMatch $m) => GameMatchResource::make($m)
                ->additional([
                    'player'     => $player,
                    'eloHistory' => $eloHistoriesByMatchId->get($m->id),
                ])
                ->resolve()
            )
            ->values();

        return Inertia::render('Player/MatchHistory', [
            'matches'         => $matches,
            'classes'         => $classes,
            'selectedClassId' => $classId,
        ]);
    }
}
