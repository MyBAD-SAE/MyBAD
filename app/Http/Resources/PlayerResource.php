<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'code' => $this->code,
            'user' => UserResource::make($this->whenLoaded('user'))->resolve(),
        ];
    }
}
