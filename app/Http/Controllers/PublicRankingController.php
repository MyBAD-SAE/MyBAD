<?php

namespace App\Http\Controllers;

use App\Models\ClassParticipant;
use App\Models\PublicView;
use App\Services\Ranking\RankingService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PublicRankingController extends Controller
{
    public function __construct(
        private readonly RankingService $rankingService,
    ) {}

    public function show(string $token): Response
    {
        $publicView = PublicView::where('access_token', $token)->firstOrFail();
        $classId = $publicView->school_class_id;

        $playerCount = ClassParticipant::forClass($classId)
            ->forPlayerType()
            ->count();

        $rankingPlayers = $this->rankingService->getRankingForClassId($classId);

        return Inertia::render('LiveRanking', [
            'token' => $token,
            'playerCount' => $playerCount,
            'rankingPlayers' => $rankingPlayers,
        ]);
    }

    public function data(string $token): JsonResponse
    {
        $publicView = PublicView::where('access_token', $token)->firstOrFail();
        $classId = $publicView->school_class_id;

        $playerCount = ClassParticipant::forClass($classId)
            ->forPlayerType()
            ->count();

        $rankingPlayers = $this->rankingService->getRankingForClassId($classId);

        return response()->json([
            'playerCount' => $playerCount,
            'rankingPlayers' => $rankingPlayers,
        ]);
    }
}
