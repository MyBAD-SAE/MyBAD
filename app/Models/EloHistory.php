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
        'player_id',
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

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
