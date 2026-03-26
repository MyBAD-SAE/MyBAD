<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperEloHistory
 */
class EloHistory extends Model
{
    protected $fillable = [
        'participant_id',
        'game_match_id',
        'elo_before',
        'elo_after',
    ];

    protected function casts(): array
    {
        return [
            'elo_before' => 'decimal:2',
            'elo_after' => 'decimal:2',
        ];
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(ClassParticipant::class);
    }

    public function gameMatch(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class);
    }
}
