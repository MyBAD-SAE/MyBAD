<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->user->full_name,
            'firstName' => $this->user->first_name,
            'avatar'    => $this->user->profile_picture,
        ];
    }
}
