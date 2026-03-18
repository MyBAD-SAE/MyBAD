<?php

namespace App\Http\Resources;

use App\Models\AdminUser;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'school_class_id'     => $this->school_class_id,
            'participantable_type' => $this->participantable_type,
            'participantable_id'  => $this->participantable_id,
            'elo_rating'          => $this->elo_rating,
            'participantable'     => $this->whenLoaded('participantable', fn () => match ($this->participantable_type) {
                Player::class    => PlayerResource::make($this->participantable),
                AdminUser::class => AdminUserResource::make($this->participantable),
                default          => $this->participantable,
            }),
        ];
    }
}
