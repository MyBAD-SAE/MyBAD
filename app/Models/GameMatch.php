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

    /**
     * Scope : matchs d'une séance impliquant un joueur donné.
     */
    public function scopeForPlayerInSession(Builder $query, string $playerId, int $sessionId): Builder
    {
        return $query->where('class_session_id', $sessionId)
            ->whereHas('players', fn (Builder $q) => $q->where('player_id', $playerId));
    }

    /**
     * Récupère les IDs des adversaires déjà affrontés par un joueur dans une séance.
     */
    public static function playedOpponentIds(string $playerId, int $sessionId): array
    {
        return self::forPlayerInSession($playerId, $sessionId)
            ->with('players')
            ->get()
            ->flatMap(fn (self $match) => $match->players->pluck('id'))
            ->reject(fn (string $id) => $id === $playerId)
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Vérifie si deux joueurs se sont déjà affrontés dans une séance.
     */
    public static function hasAlreadyPlayed(string $playerId, string $opponentId, int $sessionId): bool
    {
        return self::forPlayerInSession($playerId, $sessionId)
            ->whereHas('players', fn (Builder $q) => $q->where('player_id', $opponentId))
            ->exists();
    }

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
