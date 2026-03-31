<?php

namespace App\Services\Player;

use App\Http\Resources\GameMatchResource;
use App\Models\EloHistory;
use App\Models\GameMatch;
use App\Models\Player;

class MatchHistoryService
{
    public function getMatches(Player $player, ?int $classId, ?int $limit = null): array
    {
        $participation = $player->selectedParticipation();

        $query = GameMatch::forPlayer($player->id)
            ->when($classId, fn ($q) => $q->forClass($classId))
            ->with(['players.user'])
            ->latest();

        if ($limit !== null) {
            $query->take($limit);
        }

        $matches = $query->get();

        $eloHistoriesByMatchId = $participation
            ? EloHistory::where('participant_id', $participation->id)
                ->whereIn('game_match_id', $matches->pluck('id'))
                ->get()
                ->keyBy('game_match_id')
            : collect();

        return $matches->map(fn (GameMatch $m) => GameMatchResource::make($m)
            ->additional([
                'player'     => $player,
                'eloHistory' => $eloHistoriesByMatchId->get($m->id),
            ])
            ->resolve()
        )->values()->all();
    }
}
