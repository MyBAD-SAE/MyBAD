<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchPlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'game_match_id' => $this->game_match_id,
            'player_id'     => $this->player_id,
            'score'         => $this->score,
            'validated'     => $this->validated,
            'player'        => PlayerResource::make($this->whenLoaded('player')),
        ];
    }
}
