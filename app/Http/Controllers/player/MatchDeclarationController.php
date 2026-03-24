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

        // Joueurs du même cours
        $participation = ClassParticipant::where('participantable_type', Player::class)
            ->where('participantable_id', $player->id)
            ->first();

        if (!$participation) {
            return response()->json(['opponents' => []], 200);
        }

        $schoolClassId = $participation->school_class_id;

        // Tous les joueurs du cours (sauf moi)
        $classPlayers = ClassParticipant::where('school_class_id', $schoolClassId)
            ->where('participantable_type', Player::class)
            ->where('participantable_id', '!=', $player->id)
            ->with('participantable.user')
            ->get();

        // Joueurs déjà affrontés dans cette séance
        $alreadyPlayed = DB::table('match_player as mp1')
            ->join('match_player as mp2', function ($join) use ($player) {
                $join->on('mp1.game_match_id', '=', 'mp2.game_match_id')
                    ->where('mp1.player_id', '=', $player->id)
                    ->whereColumn('mp2.player_id', '!=', 'mp1.player_id');
            })
            ->join('game_matches as gm', 'mp1.game_match_id', '=', 'gm.id')
            ->where('gm.class_session_id', $activeSession->id)
            ->pluck('mp2.player_id')
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
            'opponents' => $opponents,
            'currentElo' => (float) $participation->elo_rating,
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
            'my_score' => ['required', 'integer', 'min:0', 'max:99'],
            'opponent_score' => ['required', 'integer', 'min:0', 'max:99'],
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
        $alreadyPlayed = DB::table('match_player as mp1')
            ->join('match_player as mp2', function ($join) use ($player) {
                $join->on('mp1.game_match_id', '=', 'mp2.game_match_id')
                    ->where('mp1.player_id', '=', $player->id)
                    ->whereColumn('mp2.player_id', '!=', 'mp1.player_id');
            })
            ->join('game_matches as gm', 'mp1.game_match_id', '=', 'gm.id')
            ->where('gm.class_session_id', $activeSession->id)
            ->where('mp2.player_id', $request->opponent_id)
            ->exists();

        if ($alreadyPlayed) {
            return response()->json(['error' => 'Vous avez déjà joué contre ce joueur dans cette séance.'], 422);
        }

        // Calculer le changement d'ELO
        $eloChange = $this->calculateEloChange(
            $player->id,
            $request->opponent_id,
            $request->my_score,
            $request->opponent_score
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
            $this->updateElo($player->id, $eloChange);
            $this->updateElo($request->opponent_id, -$eloChange);

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
        $participation = ClassParticipant::where('participantable_type', Player::class)
            ->where('participantable_id', $player->id)
            ->first();

        if (!$participation) {
            return null;
        }

        return ClassSession::where('school_class_id', $participation->school_class_id)
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
    private function calculateEloChange(string $playerId, string $opponentId, int $myScore, int $opponentScore): float
    {
        if ($myScore === $opponentScore) {
            return 0;
        }

        $participation = ClassParticipant::where('participantable_type', Player::class)
            ->where('participantable_id', $playerId)
            ->first();

        if (!$participation) {
            return 0;
        }

        $schoolClassId = $participation->school_class_id;

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
        $rankDiff = $loserRank - $winnerRank;

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
    private function updateElo(string $playerId, float $eloChange): void
    {
        $participation = ClassParticipant::where('participantable_type', Player::class)
            ->where('participantable_id', $playerId)
            ->first();

        if (!$participation) {
            return;
        }

        $eloBefore = (float) $participation->elo_rating;
        $eloAfter = $eloBefore + $eloChange;

        $participation->update(['elo_rating' => $eloAfter]);

        EloHistory::create([
            'player_id' => $playerId,
            'elo_before' => $eloBefore,
            'elo_after' => $eloAfter,
        ]);
    }
}
