<?php

namespace App\Services\Ranking;

use App\Models\ClassParticipant;
use App\Models\EloHistory;
use App\Models\GameMatch;
use App\Models\Player;
use Illuminate\Support\Collection;

class RankingService
{
    /**
     * Retourne le classement complet d'une classe avec stats et tendances.
     */
    public function getRankingForClass(Player $player, ?int $classId = null): array
    {
        $participation = ClassParticipant::forPlayer($player->id)
            ->when($classId, fn ($q) => $q->forClass($classId))
            ->first();

        if (!$participation) {
            return [];
        }

        return $this->getRankingForClassId($participation->school_class_id);
    }

    /**
     * Retourne le classement complet d'une classe par son ID.
     */
    public function getRankingForClassId(int $classId): array
    {
        $participants = ClassParticipant::forClass($classId)
            ->forPlayerType()
            ->with('participantable.user.adminUser')
            ->orderByDesc('elo_rating')
            ->get();

        $playerIds = $participants->pluck('participantable_id');
        $matchStats = $this->getBulkMatchStats($playerIds, $classId);
        $eloTrends = $this->getBulkEloTrends($participants);

        return $participants->values()->map(function (ClassParticipant $participant, int $index) use ($matchStats, $eloTrends) {
            $playerId = $participant->participantable_id;
            $user = $participant->participantable->user;

            $wins = $matchStats[$playerId]['wins'] ?? 0;
            $losses = $matchStats[$playerId]['losses'] ?? 0;
            $total = $wins + $losses;

            return [
                'participantId' => $participant->id,
                'userId'  => $user->id,
                'rank'    => $index + 1,
                'name'    => $user->full_name,
                'avatar'  => $user->profile_picture,
                'elo'     => (float) $participant->elo_rating,
                'isActive' => (bool) $user->is_active,
                'isAdmin'  => $user->adminUser !== null,
                'wins'    => $wins,
                'losses'  => $losses,
                'trend'   => $eloTrends[$playerId] ?? 0,
                'winRate' => $total > 0 ? round(($wins / $total) * 100) : 0,
            ];
        })->all();
    }

    /**
     * Récupère les stats win/loss de tous les joueurs d'une classe en une seule requête.
     */
    private function getBulkMatchStats(Collection $playerIds, int $schoolClassId): array
    {
        $stats = $playerIds->mapWithKeys(fn ($id) => [$id => ['wins' => 0, 'losses' => 0]])->toArray();

        GameMatch::forClass($schoolClassId)
            ->with('players')
            ->get()
            ->each(function (GameMatch $match) use (&$stats) {
                if ($match->players->count() !== 2) {
                    return;
                }

                $a = $match->players->first();
                $b = $match->players->last();

                if ($a->pivot->score > $b->pivot->score) {
                    $stats[$a->id]['wins']++;
                    $stats[$b->id]['losses']++;
                } elseif ($b->pivot->score > $a->pivot->score) {
                    $stats[$b->id]['wins']++;
                    $stats[$a->id]['losses']++;
                }
            });

        return $stats;
    }

    /**
     * Récupère la tendance ELO globale de chaque joueur.
     */
    private function getBulkEloTrends(Collection $participants): array
    {
        $participantToPlayer = $participants->pluck('participantable_id', 'id');

        return EloHistory::whereIn('participant_id', $participantToPlayer->keys())
            ->get()
            ->groupBy('participant_id')
            ->mapWithKeys(fn (Collection $histories, int $participantId) => [
                $participantToPlayer[$participantId] => round(
                    $histories->sum(fn (EloHistory $h) => $h->elo_after - $h->elo_before),
                    1
                ),
            ])
            ->toArray();
    }
}
