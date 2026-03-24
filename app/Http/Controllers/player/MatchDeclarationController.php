<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Player\StoreMatchRequest;
use App\Http\Requests\Player\VerifyPinRequest;
use App\Models\ClassParticipant;
use App\Models\ClassSession;
use App\Models\GameMatch;
use App\Models\Player;
use App\Services\EloService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class MatchDeclarationController extends Controller
{
    public function __construct(
        private readonly EloService $eloService,
    ) {}

    /**
     * Affiche la page de déclaration de match avec les données nécessaires.
     */
    public function create(): Response
    {
        $user = Auth::guard('player')->user();
        $player = $user->player;
        $activeSession = $this->getActiveSession($player);

        return Inertia::render('Player/DeclarationMatch', [
            'currentPlayer' => [
                'id' => $player->id,
                'name' => $user->full_name,
                'firstName' => $user->first_name,
                'avatar' => $user->profile_picture,
            ],
            'activeSession' => $activeSession?->id,
        ]);
    }

    /**
     * Retourne la liste des adversaires disponibles pour la séance active.
     */
    public function opponents(): JsonResponse
    {
        $user = Auth::guard('player')->user();
        $player = $user->player;
        $activeSession = $this->getActiveSession($player);

        if (!$activeSession) {
            return response()->json(['opponents' => [], 'error' => 'Aucune séance active.'], 200);
        }

        $participation = $player->classParticipants()->first();

        if (!$participation) {
            return response()->json(['opponents' => []], 200);
        }

        // Adversaires déjà affrontés dans cette séance
        $alreadyPlayedIds = GameMatch::playedOpponentIds($player->id, $activeSession->id);

        // Tous les joueurs du même cours (sauf moi)
        $opponents = ClassParticipant::where('school_class_id', $participation->school_class_id)
            ->where('participantable_type', Player::class)
            ->where('participantable_id', '!=', $player->id)
            ->with('participantable.user')
            ->get()
            ->map(fn (ClassParticipant $cp) => [
                'id' => $cp->participantable->id,
                'name' => $cp->participantable->user->full_name,
                'firstName' => $cp->participantable->user->first_name,
                'avatar' => $cp->participantable->user->profile_picture,
                'elo' => (float) $cp->elo_rating,
                'already_played' => in_array($cp->participantable->id, $alreadyPlayedIds),
            ])
            ->sortBy('already_played')
            ->values();

        return response()->json([
            'opponents' => $opponents,
            'currentElo' => (float) $participation->elo_rating,
        ]);
    }

    /**
     * Vérifie le PIN de l'adversaire.
     */
    public function verifyPin(VerifyPinRequest $request): JsonResponse
    {
        $opponent = Player::findOrFail($request->player_id);

        if (!$opponent->pin) {
            return response()->json([
                'valid' => false,
                'message' => "Ce joueur n'a pas encore défini de code PIN.",
            ]);
        }

        $valid = Hash::check($request->pin, $opponent->pin);

        return response()->json(['valid' => $valid]);
    }

    /**
     * Enregistre le match en base de données.
     */
    public function store(StoreMatchRequest $request): JsonResponse
    {
        $user = Auth::guard('player')->user();
        $player = $user->player;
        $activeSession = $this->getActiveSession($player);

        if (!$activeSession) {
            return response()->json(['error' => 'Aucune séance active.'], 422);
        }

        // Vérifier que l'adversaire n'a pas déjà été affronté dans cette séance
        if (GameMatch::hasAlreadyPlayed($player->id, $request->opponent_id, $activeSession->id)) {
            return response()->json(['error' => 'Vous avez déjà joué contre ce joueur dans cette séance.'], 422);
        }

        // Calculer le changement d'ELO
        $eloChange = $this->eloService->calculateEloChange(
            $player->id,
            $request->opponent_id,
            $request->my_score,
            $request->opponent_score,
        );

        // Créer le match et les entrées pivot dans une transaction
        $match = DB::transaction(function () use ($request, $player, $activeSession, $eloChange) {
            $match = GameMatch::create([
                'class_session_id' => $activeSession->id,
            ]);

            // Joueur courant
            $match->players()->attach($player->id, [
                'score' => $request->my_score,
                'validated' => true,
            ]);

            // Adversaire
            $match->players()->attach($request->opponent_id, [
                'score' => $request->opponent_score,
                'validated' => true,
            ]);

            // Mettre à jour l'ELO des deux joueurs
            $this->eloService->updateElo($player->id, $eloChange);
            $this->eloService->updateElo($request->opponent_id, -$eloChange);

            return $match;
        });

        return response()->json([
            'success' => true,
            'eloChange' => $eloChange,
            'match_id' => $match->id,
        ]);
    }

    /**
     * Récupère la séance active pour le joueur.
     */
    private function getActiveSession(Player $player): ?ClassSession
    {
        $participation = $player->classParticipants()->first();

        if (!$participation) {
            return null;
        }

        return ClassSession::where('school_class_id', $participation->school_class_id)
            ->active()
            ->first();
    }
}
