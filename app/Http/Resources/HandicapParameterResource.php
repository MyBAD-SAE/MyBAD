<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HandicapParameterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'min_gap' => $this->min_gap,
            'max_gap' => $this->max_gap,
            'handicap' => $this->handicap,
        ];
    }
}