<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin IdeHelperPlayer
 */
class Player extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'pin',
        'code',
    ];

    protected $hidden = [
        'pin',
    ];

    protected function casts(): array
    {
        return [
            'pin' => 'hashed',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classParticipants(): MorphMany
    {
        return $this->morphMany(ClassParticipant::class, 'participantable');
    }

    /**
     * Retourne le ClassParticipant correspondant à la classe sélectionnée en session.
     * Fallback sur le premier cours du joueur si la session est invalide.
     * Résultat memoïsé pour toute la durée de la requête.
     */
    public function selectedParticipation(): ?ClassParticipant
    {
        return once(function () {
            $classIds = $this->classParticipants()->pluck('school_class_id');

            if ($classIds->isEmpty()) {
                return null;
            }

            $sessionId = (int) session('selected_class_id');
            $classId   = $classIds->contains($sessionId) ? $sessionId : (int) $classIds->first();

            return $this->classParticipants()
                ->where('school_class_id', $classId)
                ->first();
        });
    }

    public function eloHistories(): Builder
    {
        $participantIds = $this->classParticipants()->pluck('id');

        return EloHistory::whereIn('participant_id', $participantIds);
    }

    public function gameMatches(): BelongsToMany
    {
        return $this->belongsToMany(GameMatch::class, 'match_player')
            ->using(MatchPlayer::class)
            ->withPivot('score', 'validated');
    }
}
