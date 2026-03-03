<?php

namespace App\Policies;

use App\Models\GameMatch;
use App\Models\User;

class MatchPolicy
{
    public function accept(User $user, GameMatch $match): bool
    {
        return $match->challenged_id === $user->id && $match->status === 'pending';
    }

    public function decline(User $user, GameMatch $match): bool
    {
        return $match->challenged_id === $user->id && $match->status === 'pending';
    }

    public function cancel(User $user, GameMatch $match): bool
    {
        return $match->challenger_id === $user->id && $match->status === 'pending';
    }

    public function update(User $user, GameMatch $match): bool
    {
        return $match->involvesUser($user) && $match->status === 'accepted';
    }
}
