<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'elo_rating',
        'matches_played',
        'matches_won',
        'matches_lost',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'elo_rating' => 'integer',
            'matches_played' => 'integer',
            'matches_won' => 'integer',
            'matches_lost' => 'integer',
        ];
    }

    public function challengesSent(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'challenger_id');
    }

    public function challengesReceived(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'challenged_id');
    }

    public function winRate(): float
    {
        if ($this->matches_played === 0) {
            return 0;
        }

        return round(($this->matches_won / $this->matches_played) * 100, 1);
    }

    public function initials(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }
        return $initials;
    }
}
