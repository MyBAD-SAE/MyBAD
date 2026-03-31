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
     * Affiche la page de déclaration de match.
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
     * Retourne la liste des adversaires disponibles pour la séance active.
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
     * Vérifie le PIN de l'adversaire.
     */
    public function verifyPin(VerifyPinRequest $request): JsonResponse
    {
        $opponent = Player::findOrFail($request->player_id);

        return response()->json($this->matchService->verifyOpponentPin($opponent, $request->pin));
    }

    /**
     * Enregistre le match en base de données.
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
            'success'   => true,
            'eloChange' => $result['eloChange'],
            'match_id'  => $result['match']->id,
        ]);
    }

    private function currentPlayer(): Player
    {
        return Auth::guard('player')->user()->player;
    }
}
