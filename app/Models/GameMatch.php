<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'challenger_id',
        'challenged_id',
        'winner_id',
        'status',
        'challenger_score_set1',
        'challenged_score_set1',
        'challenger_score_set2',
        'challenged_score_set2',
        'challenger_score_set3',
        'challenged_score_set3',
        'played_at',
        'elo_change',
    ];

    protected function casts(): array
    {
        return [
            'played_at' => 'datetime',
        ];
    }

    public function challenger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'challenger_id');
    }

    public function challenged(): BelongsTo
    {
        return $this->belongsTo(User::class, 'challenged_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function opponent(User $user): ?User
    {
        if ($user->id === $this->challenger_id) {
            return $this->challenged;
        }
        if ($user->id === $this->challenged_id) {
            return $this->challenger;
        }
        return null;
    }

    public function involvesUser(User $user): bool
    {
        return $this->challenger_id === $user->id || $this->challenged_id === $user->id;
    }

    public function setsWonByChallenger(): int
    {
        $sets = 0;
        if ($this->challenger_score_set1 !== null && $this->challenger_score_set1 > $this->challenged_score_set1) $sets++;
        if ($this->challenger_score_set2 !== null && $this->challenger_score_set2 > $this->challenged_score_set2) $sets++;
        if ($this->challenger_score_set3 !== null && $this->challenger_score_set3 > $this->challenged_score_set3) $sets++;
        return $sets;
    }

    public function setsWonByChallenged(): int
    {
        $sets = 0;
        if ($this->challenged_score_set1 !== null && $this->challenged_score_set1 > $this->challenger_score_set1) $sets++;
        if ($this->challenged_score_set2 !== null && $this->challenged_score_set2 > $this->challenger_score_set2) $sets++;
        if ($this->challenged_score_set3 !== null && $this->challenged_score_set3 > $this->challenger_score_set3) $sets++;
        return $sets;
    }

    public function scoreDisplay(): string
    {
        $sets = [];
        if ($this->challenger_score_set1 !== null) {
            $sets[] = $this->challenger_score_set1 . '-' . $this->challenged_score_set1;
        }
        if ($this->challenger_score_set2 !== null) {
            $sets[] = $this->challenger_score_set2 . '-' . $this->challenged_score_set2;
        }
        if ($this->challenger_score_set3 !== null) {
            $sets[] = $this->challenger_score_set3 . '-' . $this->challenged_score_set3;
        }
        return implode(' / ', $sets);
    }
}
