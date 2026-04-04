<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'enable_ranking_groups' => $this->enable_ranking_groups,
            'enable_elo_handicap' => $this->enable_elo_handicap,
            'group_size' => $this->group_size,
            'handicap_parameters' => HandicapParameterResource::collection($this->whenLoaded('handicapParameters')),
        ];
    }
}