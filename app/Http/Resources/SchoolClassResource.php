<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolClassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_year' => $this->school_year,
            'name' => $this->name,
            'address' => $this->address,
            'description' => $this->description,
            'algorithm_parameters' => AlgorithmParameterResource::collection($this->whenLoaded('algorithmParameters')),
            'participants' => ClassParticipantResource::collection($this->whenLoaded('participants')),
        ];
    }
}
