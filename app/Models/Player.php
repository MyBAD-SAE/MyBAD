<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function eloHistories(): HasMany
    {
        return $this->hasMany(EloHistory::class);
    }

    public function gameMatches(): BelongsToMany
    {
        return $this->belongsToMany(GameMatch::class, 'match_player')
            ->using(MatchPlayer::class)
            ->withPivot('score', 'validated');
    }
}
