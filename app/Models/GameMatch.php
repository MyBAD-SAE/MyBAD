<?php

namespace App\Models;

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



}
