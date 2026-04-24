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

    /**
     * @OA\Get(
     *     path="/historique-matchs",
     *     tags={"Joueur - Matchs"},
     *     summary="Historique des matchs du joueur",
     *     operationId="player.matches.history",
     *     security={{"session":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Props Inertia de la page historique",
     *         @OA\JsonContent(
     *             @OA\Property(property="matches", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="classes", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="selectedClassId", type="integer", nullable=true)
     *         )
     *     )
     * )
     */
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
