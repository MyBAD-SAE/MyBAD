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

    /**
     * @OA\Get(
     *     path="/",
     *     tags={"Joueur - Tableau de bord"},
     *     summary="Tableau de bord joueur",
     *     operationId="player.dashboard",
     *     security={{"session":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Props Inertia du tableau de bord joueur",
     *         @OA\JsonContent(
     *             @OA\Property(property="rankingPlayers", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=302, description="Redirection vers /player/login si non authentifié")
     * )
     */
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
