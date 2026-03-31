<?php

namespace App\Services\Match;

use App\Http\Resources\PlayerCardResource;
use App\Models\ClassParticipant;
use App\Models\ClassSession;
use App\Models\GameMatch;
use App\Models\Player;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MatchDeclarationService
{
    public function __construct(
        private readonly EloService $eloService,
    ) {}

    /**
     * Récupère la séance active pour le joueur dans sa classe sélectionnée.
     */
    public function getActiveSession(Player $player): ?ClassSession
    {
        $classId = $player->selectedParticipation()?->school_class_id;

        if (!$classId) {
            return null;
        }

        return ClassSession::forClass($classId)
            ->active()
            ->latest('date')
            ->first();
    }

    /**
     * Retourne la liste des adversaires disponibles pour la séance active.
     */
    public function getOpponents(Player $player, ClassSession $session): Collection
    {
        $alreadyPlayedIds = $this->getAlreadyPlayedIds($player, $session);

        return ClassParticipant::forClass($session->school_class_id)
            ->forPlayerType()
            ->where('participantable_id', '!=', $player->id)
            ->with('participantable.user')
            ->get()
            ->map(fn (ClassParticipant $cp) => [
                ...PlayerCardResource::make($cp->participantable)->resolve(),
                'elo'            => (float) $cp->elo_rating,
                'already_played' => $alreadyPlayedIds->contains($cp->participantable->id),
            ])
            ->sortBy('already_played')
            ->values();
    }

    /**
     * Vérifie le PIN d'un adversaire.
     */
    public function verifyOpponentPin(Player $opponent, string $pin): array
    {
        if (!$opponent->pin) {
            return [
                'valid'   => false,
                'message' => "Ce joueur n'a pas encore défini de code PIN.",
            ];
        }

        return ['valid' => Hash::check($pin, $opponent->pin)];
    }

    /**
     * Vérifie si deux joueurs se sont déjà affrontés dans une séance.
     */
    public function hasAlreadyPlayed(Player $player, string $opponentId, ClassSession $session): bool
    {
        return GameMatch::where('class_session_id', $session->id)
            ->whereHas('players', fn ($q) => $q->where('player_id', $player->id))
            ->whereHas('players', fn ($q) => $q->where('player_id', $opponentId))
            ->exists();
    }

    /**
     * Crée un match et met à jour les ELO des deux joueurs.
     */
    public function storeMatch(
        Player $player,
        string $opponentId,
        int $myScore,
        int $opponentScore,
        ClassSession $session,
    ): array {
        $schoolClassId = $session->school_class_id;

        $eloChangePlayer = $this->eloService->calculateEloChange(
            $player->id,
            $opponentId,
            $myScore,
            $opponentScore,
            $schoolClassId,
        );

        $eloChangeOpponent = $this->eloService->calculateEloChange(
            $opponentId,
            $player->id,
            $opponentScore,
            $myScore,
            $schoolClassId,
        );

        $match = DB::transaction(function () use ($player, $opponentId, $myScore, $opponentScore, $session, $eloChangePlayer, $eloChangeOpponent, $schoolClassId) {
            $match = $session->gameMatches()->create();

            $match->players()->attach([
                $player->id => ['score' => $myScore, 'validated' => true],
                $opponentId => ['score' => $opponentScore, 'validated' => true],
            ]);

            $this->eloService->updateElo($player->id, $eloChangePlayer, $schoolClassId, $match->id);
            $this->eloService->updateElo($opponentId, $eloChangeOpponent, $schoolClassId, $match->id);

            return $match;
        });

        return [
            'match'              => $match,
            'eloChange'          => $eloChangePlayer,
            'eloChangeOpponent'  => $eloChangeOpponent,
        ];
    }

    /**
     * Récupère les IDs des joueurs déjà affrontés dans la séance.
     */
    private function getAlreadyPlayedIds(Player $player, ClassSession $session): Collection
    {
        return $session->gameMatches()
            ->whereHas('players', fn ($q) => $q->where('player_id', $player->id))
            ->with('players')
            ->get()
            ->flatMap(fn (GameMatch $match) => $match->players->where('id', '!=', $player->id)->pluck('id'));
    }
}