<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MatchPlayer extends Pivot
{
    protected $table = 'match_player';

    protected $fillable = [
        'game_match_id',
        'player_id',
        'score',
        'validated',
    ];

    protected function casts(): array
    {
        return [
            'validated' => 'boolean',
        ];
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function gameMatch()
    {
        return $this->belongsTo(GameMatch::class, 'game_match_id');
    }
}