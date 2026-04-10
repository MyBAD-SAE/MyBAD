<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperClassParticipant
 */
class ClassParticipant extends Model
{
    protected $fillable = [
        'participantable_type',
        'participantable_id',
        'elo_rating',
        'school_class_id',
    ];

    protected function casts(): array
    {
        return [
            'elo_rating' => 'decimal:1',
        ];
    }

    public function participantable(): MorphTo
    {
        return $this->morphTo();
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function eloHistories(): HasMany
    {
        return $this->hasMany(EloHistory::class, 'participant_id');
    }

    // filtre les participants d'une classe précise
    public function scopeForClass(Builder $query, int $classId): Builder
    {
        return $query->where('school_class_id', $classId);
    }

    // filtre sur un joueur spécifique
    public function scopeForPlayer(Builder $query, string $playerId): Builder
    {
        return $query->where('participantable_type', Player::class)
            ->where('participantable_id', $playerId);
    }

    // filtre tous les participants qui sont des joueurs, et exclut les admins
    public function scopeForPlayerType(Builder $query): Builder
    {
        return $query->where('participantable_type', Player::class);
    }
}
