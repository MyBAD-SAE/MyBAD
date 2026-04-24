<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Services\Player\EloDetailsService;
use App\Services\Player\PlayerProfileService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class EloDetailsController extends Controller
{
    public function __construct(
        private readonly EloDetailsService $eloDetailsService,
        private readonly PlayerProfileService $profileService,
    ) {}

    /**
     * @OA\Get(
     *     path="/elo-details",
     *     tags={"Joueur - Classement"},
     *     summary="Historique ELO détaillé du joueur",
     *     operationId="player.elo.details",
     *     security={{"session":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Props Inertia de la page détail ELO",
     *         @OA\JsonContent(
     *             @OA\Property(property="classes", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="selectedClassId", type="integer", nullable=true)
     *         )
     *     )
     * )
     */
    public function index(): Response
    {
        $user          = Auth::guard('player')->user();
        $player        = $user->player;
        $participation = $player?->selectedParticipation()?->load('participantable.user');
        $classId       = $participation?->school_class_id;

        return Inertia::render('Player/EloDetails', [
            ...$this->eloDetailsService->getData($participation, $classId),
            'classes'         => $this->profileService->getPlayerClasses($player),
            'selectedClassId' => $classId,
        ]);
    }
}
