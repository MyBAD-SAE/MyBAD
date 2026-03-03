<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $pendingChallenges = GameMatch::where('challenged_id', $user->id)
            ->where('status', 'pending')
            ->with('challenger')
            ->latest()
            ->get();

        $recentMatches = GameMatch::where(function ($q) use ($user) {
                $q->where('challenger_id', $user->id)
                  ->orWhere('challenged_id', $user->id);
            })
            ->where('status', 'completed')
            ->with(['challenger', 'challenged', 'winner'])
            ->latest('played_at')
            ->take(5)
            ->get();

        $rank = \App\Models\User::where('elo_rating', '>', $user->elo_rating)->count() + 1;

        return Inertia::render('Dashboard', [
            'stats' => [
                'matches_played' => $user->matches_played,
                'matches_won' => $user->matches_won,
                'matches_lost' => $user->matches_lost,
                'win_rate' => $user->winRate(),
                'elo_rating' => $user->elo_rating,
                'rank' => $rank,
            ],
            'pendingChallenges' => $pendingChallenges,
            'recentMatches' => $recentMatches,
        ]);
    }
}
