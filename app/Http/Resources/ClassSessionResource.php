<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_class_id' => $this->school_class_id,
            'date' => $this->date,
            'session_name' => $this->session_name,
            'is_active' => $this->is_active,
            'school_class' => SchoolClassResource::make($this->whenLoaded('schoolClass')),
            'game_matches' => GameMatchResource::collection($this->whenLoaded('gameMatches')),
        ];
    }
}
