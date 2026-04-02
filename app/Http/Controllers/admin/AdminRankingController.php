<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ClassParticipant;
use App\Services\Ranking\RankingService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminRankingController extends Controller
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

        $playerCount = 0;
        $rankingPlayers = [];
        $enableRankingGroups = false;
        $groupSize = 8;

        if ($selectedClassId) {
            $playerCount = ClassParticipant::forClass($selectedClassId)
                ->forPlayerType()
                ->count();

            $rankingPlayers = $this->rankingService->getRankingForClassId($selectedClassId);
        }

        return Inertia::render('Admin/Ranking', [
            'classes'              => $classes,
            'selectedClassId'      => $selectedClassId,
            'playerCount'          => $playerCount,
            'rankingPlayers'       => $rankingPlayers,
            'enableRankingGroups'  => $enableRankingGroups,
            'groupSize'            => $groupSize,
        ]);
    }

    public function data(): JsonResponse
    {
        $user = auth('admin')->user();
        $adminUser = $user->adminUser;

        $selectedClassId = session('admin_selected_class_id');

        if (!$selectedClassId) {
            $firstClass = $adminUser->classParticipants()
                ->with('schoolClass')
                ->first();
            $selectedClassId = $firstClass?->schoolClass?->id;
        }

        $playerCount = 0;
        $rankingPlayers = [];

        if ($selectedClassId) {
            $playerCount = ClassParticipant::forClass($selectedClassId)
                ->forPlayerType()
                ->count();

            $rankingPlayers = $this->rankingService->getRankingForClassId($selectedClassId);
        }

        return response()->json([
            'playerCount'    => $playerCount,
            'rankingPlayers' => $rankingPlayers,
        ]);
    }
}