<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperGameMatch
 */
class GameMatch extends Model
{
    protected $fillable = [
        'class_session_id',
    ];

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id');
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'match_player')
            ->using(MatchPlayer::class)
            ->withPivot('score', 'validated')
            ->withTimestamps();
    }

    public function scopeForClass(Builder $query, int $classId): Builder
    {
        return $query->whereHas('classSession', fn (Builder $q) => $q->where('school_class_id', $classId));
    }

    public function scopeForPlayer(Builder $query, string $playerId): Builder
    {
        return $query->whereHas('players', fn (Builder $q) => $q->where('player_id', $playerId));
    }
}
