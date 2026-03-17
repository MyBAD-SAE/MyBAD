<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlgorithmParameterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_class_id' => $this->school_class_id,
            'min_diff' => $this->min_diff,
            'max_diff' => $this->max_diff,
            'winner_points' => $this->winner_points,
        ];
    }
}
