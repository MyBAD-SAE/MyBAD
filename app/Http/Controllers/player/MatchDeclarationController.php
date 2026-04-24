<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Player\StoreMatchRequest;
use App\Http\Requests\Player\VerifyPinRequest;
use App\Http\Resources\PlayerCardResource;
use App\Models\Player;
use App\Services\Match\MatchDeclarationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MatchDeclarationController extends Controller
{
    public function __construct(
        private readonly MatchDeclarationService $matchService,
    ) {}

    /**
     * @OA\Get(
     *     path="/declarer-un-match",
     *     tags={"Joueur - Matchs"},
     *     summary="Page de déclaration de match",
     *     operationId="player.match.declare",
     *     security={{"session":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Props Inertia de la page déclaration",
     *         @OA\JsonContent(
     *             @OA\Property(property="currentPlayer", type="object"),
     *             @OA\Property(property="activeSession", type="integer", nullable=true, description="ID de la séance active")
     *         )
     *     )
     * )
     */
    public function create(): Response
    {
        $player = $this->currentPlayer();

        return Inertia::render('Player/MatchDeclaration', [
            'currentPlayer' => PlayerCardResource::make($player->load('user'))->resolve(),
            'activeSession' => $this->matchService->getActiveSession($player)?->id,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/declarer-un-match/adversaires",
     *     tags={"Joueur - Matchs"},
     *     summary="Liste des adversaires disponibles dans la séance active",
     *     operationId="player.match.opponents",
     *     security={{"session":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des adversaires et ELO courant du joueur",
     *         @OA\JsonContent(
     *             @OA\Property(property="opponents", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="currentElo", type="number"),
     *             @OA\Property(property="error", type="string", nullable=true, description="Message d'erreur si aucune séance active")
     *         )
     *     )
     * )
     */
    public function opponents(): JsonResponse
    {
        $player = $this->currentPlayer();
        $activeSession = $this->matchService->getActiveSession($player);

        if (!$activeSession) {
            return response()->json(['opponents' => [], 'error' => 'Aucune séance active.']);
        }

        return response()->json([
            'opponents'  => $this->matchService->getOpponents($player, $activeSession),
            'currentElo' => (float) $player->selectedParticipation()?->elo_rating,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/declarer-un-match/verify-pin",
     *     tags={"Joueur - Matchs"},
     *     summary="Vérifier le PIN de l'adversaire",
     *     operationId="player.match.verifyPin",
     *     security={{"session":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player_id","pin"},
     *             @OA\Property(property="player_id", type="integer", description="ID du joueur adverse"),
     *             @OA\Property(property="pin", type="string", minLength=4, maxLength=4, example="1234")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultat de la vérification",
     *         @OA\JsonContent(
     *             @OA\Property(property="valid", type="boolean"),
     *             @OA\Property(property="message", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function verifyPin(VerifyPinRequest $request): JsonResponse
    {
        $opponent = Player::findOrFail($request->player_id);

        return response()->json($this->matchService->verifyOpponentPin($opponent, $request->pin));
    }

    /**
     * @OA\Post(
     *     path="/declarer-un-match",
     *     tags={"Joueur - Matchs"},
     *     summary="Enregistrer un match et recalculer les ELO",
     *     operationId="player.match.store",
     *     security={{"session":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"opponent_id","my_score","opponent_score"},
     *             @OA\Property(property="opponent_id", type="integer"),
     *             @OA\Property(property="my_score", type="integer", minimum=0, maximum=21, example=15),
     *             @OA\Property(property="opponent_score", type="integer", minimum=0, maximum=21, example=10,
     *                 description="Au moins un des deux scores doit être ≥ 15"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Match enregistré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="eloChange", type="number", description="Variation d'ELO du joueur courant"),
     *             @OA\Property(property="eloChangeOpponent", type="number"),
     *             @OA\Property(property="match_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Aucune séance active, déjà joué contre cet adversaire, ou scores invalides")
     * )
     */
    public function store(StoreMatchRequest $request): JsonResponse
    {
        $player = $this->currentPlayer();
        $activeSession = $this->matchService->getActiveSession($player);

        if (!$activeSession) {
            return response()->json(['error' => 'Aucune séance active.'], 422);
        }

        if ($this->matchService->hasAlreadyPlayed($player, $request->opponent_id, $activeSession)) {
            return response()->json(['error' => 'Vous avez déjà joué contre ce joueur dans cette séance.'], 422);
        }

        $result = $this->matchService->storeMatch(
            player: $player,
            opponentId: $request->opponent_id,
            myScore: $request->my_score,
            opponentScore: $request->opponent_score,
            session: $activeSession,
        );

        return response()->json([
            'success'            => true,
            'eloChange'          => $result['eloChange'],
            'eloChangeOpponent'  => $result['eloChangeOpponent'],
            'match_id'           => $result['match']->id,
        ]);
    }

    private function currentPlayer(): Player
    {
        return Auth::guard('player')->user()->player;
    }
}
