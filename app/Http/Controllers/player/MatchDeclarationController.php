<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Models\AlgorithmParameter;
use App\Models\ClassParticipant;
use App\Models\ClassSession;
use App\Models\EloHistory;
use App\Models\GameMatch;
use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class MatchDeclarationController extends Controller
{
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
     * = joueurs du même cours, pas encore affrontés dans cette séance.
     */
    public function opponents(Request $request): JsonResponse
    {
        $user = Auth::guard('player')->user();
        $player = $user->player;

        $activeSession = $this->getActiveSession($player);

        if (!$activeSession) {
            return response()->json(['opponents' => [], 'error' => 'Aucune séance active.'], 200);
        }

        $schoolClassId = $activeSession->school_class_id;

        // Tous les joueurs du cours (sauf moi)
        $classPlayers = ClassParticipant::where('school_class_id', $schoolClassId)
            ->where('participantable_type', Player::class)
            ->where('participantable_id', '!=', $player->id)
            ->with('participantable.user')
            ->get();

        // Joueurs déjà affrontés dans cette séance
        $alreadyPlayed = GameMatch::where('class_session_id', $activeSession->id)
            ->whereHas('players', fn ($q) => $q->where('player_id', $player->id))
            ->with('players')
            ->get()
            ->flatMap(fn ($match) => $match->players->where('id', '!=', $player->id)->pluck('id'))
            ->toArray();

        $opponents = $classPlayers
            ->map(function ($cp) use ($alreadyPlayed) {
                $opponentPlayer = $cp->participantable;
                $opponentUser = $opponentPlayer->user;

                return [
                    'id' => $opponentPlayer->id,
                    'name' => $opponentUser->full_name,
                    'firstName' => $opponentUser->first_name,
                    'avatar' => $opponentUser->profile_picture,
                    'elo' => (float) $cp->elo_rating,
                    'already_played' => in_array($opponentPlayer->id, $alreadyPlayed),
                ];
            })
            ->sortBy('already_played')
            ->values();

        return response()->json([
            'opponents'  => $opponents,
            'currentElo' => (float) $player->selectedParticipation()?->elo_rating,
        ]);
    }

    /**
     * Vérifie le PIN de l'adversaire.
     */
    public function verifyPin(Request $request): JsonResponse
    {
        $request->validate([
            'player_id' => ['required', 'exists:players,id'],
            'pin' => ['required', 'string', 'size:4'],
        ]);

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
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'opponent_id' => ['required', 'exists:players,id'],
            'my_score' => ['required', 'integer', 'min:0', 'max:21'],
            'opponent_score' => ['required', 'integer', 'min:0', 'max:21'],
        ]);

        // Au moins un des deux scores doit être >= 15
        if ($request->my_score < 15 && $request->opponent_score < 15) {
            return response()->json([
                'error' => 'Au moins un des deux scores doit être de 15 points minimum.',
            ], 422);
        }

        $user = Auth::guard('player')->user();
        $player = $user->player;

        $activeSession = $this->getActiveSession($player);

        if (!$activeSession) {
            return response()->json(['error' => 'Aucune séance active.'], 422);
        }

        // Vérifier que l'adversaire n'a pas déjà été affronté dans cette séance
        $alreadyPlayed = GameMatch::where('class_session_id', $activeSession->id)
            ->whereHas('players', fn ($q) => $q->where('player_id', $player->id))
            ->whereHas('players', fn ($q) => $q->where('player_id', $request->opponent_id))
            ->exists();

        if ($alreadyPlayed) {
            return response()->json(['error' => 'Vous avez déjà joué contre ce joueur dans cette séance.'], 422);
        }

        $schoolClassId = $activeSession->school_class_id;

        // Calculer le changement d'ELO
        $eloChange = $this->calculateEloChange(
            $player->id,
            $request->opponent_id,
            $request->my_score,
            $request->opponent_score,
            $schoolClassId,
        );

        // Créer le match et les entrées pivot dans une transaction
        $match = DB::transaction(function () use ($request, $player, $activeSession, $eloChange, $schoolClassId) {
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

            // Mettre à jour l'ELO des deux joueurs dans la bonne classe
            $this->updateElo($player->id, $eloChange, $schoolClassId);
            $this->updateElo($request->opponent_id, -$eloChange, $schoolClassId);

            return $match;
        });

        return response()->json([
            'success' => true,
            'eloChange' => $eloChange,
            'match_id' => $match->id,
        ]);
    }

    /**
     * Récupère la séance active pour le joueur dans sa classe sélectionnée.
     */
    private function getActiveSession(Player $player): ?ClassSession
    {
        $classId = $player->selectedParticipation()?->school_class_id;

        if (!$classId) {
            return null;
        }

        return ClassSession::where('school_class_id', $classId)
            ->where('is_active', true)
            ->latest('date')
            ->first();
    }

    /**
     * Calcule le changement d'ELO basé sur l'écart de rang entre les joueurs.
     *
     * Formule : winner_points (depuis algorithm_parameters) + écart_rang / 10
     * Plafonné entre 0 et 10. Le perdant reçoit l'inverse.
     */
    private function calculateEloChange(string $playerId, string $opponentId, int $myScore, int $opponentScore, int $schoolClassId): float
    {
        if ($myScore === $opponentScore) {
            return 0;
        }

        // Classement de tous les joueurs de la classe par ELO décroissant
        $rankings = ClassParticipant::where('school_class_id', $schoolClassId)
            ->where('participantable_type', Player::class)
            ->orderByDesc('elo_rating')
            ->pluck('participantable_id')
            ->values();

        $myRank = $rankings->search($playerId);
        $opponentRank = $rankings->search($opponentId);

        if ($myRank === false || $opponentRank === false) {
            return 0;
        }

        // Rang commence à 1
        $myRank += 1;
        $opponentRank += 1;

        // Déterminer le vainqueur et le perdant
        $isWinner = $myScore > $opponentScore;

        // Écart de rang du point de vue du vainqueur : positif = le perdant était mieux classé
        $winnerRank = $isWinner ? $myRank : $opponentRank;
        $loserRank = $isWinner ? $opponentRank : $myRank;
        $rankDiff = $winnerRank - $loserRank;

        // Récupérer les paramètres de l'algorithme pour cette classe
        $param = AlgorithmParameter::where('school_class_id', $schoolClassId)
            ->where('min_diff', '<=', $rankDiff)
            ->where('max_diff', '>=', $rankDiff)
            ->first();

        if (!$param) {
            $param = AlgorithmParameter::where('school_class_id', $schoolClassId)
                ->orderByRaw('ABS(min_diff + max_diff - ?) ASC', [$rankDiff * 2])
                ->first();
        }

        $basePoints = $param ? $param->winner_points : 0.5;
        $winnerChange = $basePoints + ($rankDiff / 10);

        // Plafonner entre 0 et 10
        $winnerChange = max(0, min(10, $winnerChange));

        // Du point de vue du joueur courant
        return $isWinner ? $winnerChange : -$winnerChange;
    }

    /**
     * Met à jour l'ELO d'un joueur et enregistre l'historique.
     */
    private function updateElo(string $playerId, float $eloChange, int $schoolClassId): void
    {
        $participation = ClassParticipant::where('participantable_type', Player::class)
            ->where('participantable_id', $playerId)
            ->where('school_class_id', $schoolClassId)
            ->first();

        if (!$participation) {
            return;
        }

        $eloBefore = (float) $participation->elo_rating;
        $eloAfter  = $eloBefore + $eloChange;

        $participation->update(['elo_rating' => $eloAfter]);

        EloHistory::create([
            'participant_id' => $participation->id,
            'elo_before'     => $eloBefore,
            'elo_after'      => $eloAfter,
        ]);
    }
}
