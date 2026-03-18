<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'class_session_id' => $this->class_session_id,
            'match_players'    => MatchPlayerResource::collection($this->whenLoaded('matchPlayers')),
        ];
    }
}
