<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ClassParticipant;
use App\Models\GameMatch;
use App\Services\Ranking\RankingService;
use Inertia\Inertia;
use Inertia\Response;

class AdminPlayersController extends Controller
{
    public function __construct(
        private readonly RankingService $rankingService,
    ) {}

    public function index(): Response
    {
        $user = auth('admin')->user();
        $adminUser = $user->adminUser;

        $classes = $adminUser->classParticipants()
            ->with('schoolClass')
            ->get()
            ->map(fn ($cp) => [
                'id'   => $cp->schoolClass->id,
                'name' => $cp->schoolClass->name,
            ])
            ->values()
            ->all();

        $selectedClassId = session('admin_selected_class_id');

        if (!$selectedClassId || !collect($classes)->contains('id', $selectedClassId)) {
            $selectedClassId = $classes[0]['id'] ?? null;
        }

        $players = [];
        $playerCount = 0;
        $matchCount = 0;
        $averageElo = 0;

        if ($selectedClassId) {
            $players = $this->rankingService->getRankingForClassId($selectedClassId);
            $playerCount = count($players);
            $matchCount = GameMatch::forClass($selectedClassId)->count();

            if ($playerCount > 0) {
                $averageElo = round(
                    ClassParticipant::forClass($selectedClassId)
                        ->forPlayerType()
                        ->avg('elo_rating'),
                    1
                );
            }
        }

        return Inertia::render('Admin/Joueurs', [
            'players'         => $players,
            'playerCount'     => $playerCount,
            'matchCount'      => $matchCount,
            'averageElo'      => $averageElo,
            'classes'         => $classes,
            'selectedClassId' => $selectedClassId,
        ]);
    }
}
