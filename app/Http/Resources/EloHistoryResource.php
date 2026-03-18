<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EloHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'player_id' => $this->player_id,
            'elo_before' => $this->elo_before,
            'elo_after' => $this->elo_after,
            'player' => PlayerResource::make($this->whenLoaded('player')),
        ];
    }
}
