<?php

namespace App\Http\Resources;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $player  = $this->additional['player'] ?? null;
        $history = $this->additional['eloHistory'] ?? null;

        if (!$player) {
            return [
                'id'               => $this->id,
                'class_session_id' => $this->class_session_id,
                'match_players'    => MatchPlayerResource::collection($this->whenLoaded('matchPlayers')),
            ];
        }

        $opponent      = $this->opponentFor($player);
        $myScore       = $this->myScoreFor($player);
        $opponentScore = $opponent?->pivot->score;
        $eloChange     = $history ? round((float) $history->elo_after - (float) $history->elo_before, 1) : 0;

        return [
            'id'            => $this->id,
            'opponent'      => $opponent ? [
                'name'   => $opponent->user->full_name,
                'avatar' => $opponent->user->profile_picture,
            ] : null,
            'myScore'       => $myScore,
            'opponentScore' => $opponentScore,
            'eloChange'     => $eloChange,
            'date'          => $this->formatted_date,
            'result'        => $myScore > $opponentScore ? 'win' : 'loss',
        ];
    }
}
