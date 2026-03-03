<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $players = User::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get(['id', 'name', 'avatar', 'elo_rating', 'matches_played', 'matches_won', 'matches_lost']);

        return Inertia::render('Players/Index', [
            'players' => $players,
            'search' => $search,
        ]);
    }

    public function show(User $user)
    {
        $matches = GameMatch::where(function ($q) use ($user) {
                $q->where('challenger_id', $user->id)
                  ->orWhere('challenged_id', $user->id);
            })
            ->where('status', 'completed')
            ->with(['challenger', 'challenged', 'winner'])
            ->latest('played_at')
            ->take(10)
            ->get();

        $rank = User::where('elo_rating', '>', $user->elo_rating)->count() + 1;

        // Head-to-head against current user
        $h2h = null;
        $authUser = auth()->user();
        if ($authUser && $authUser->id !== $user->id) {
            $h2hMatches = GameMatch::where('status', 'completed')
                ->where(function ($q) use ($user, $authUser) {
                    $q->where(function ($q2) use ($user, $authUser) {
                        $q2->where('challenger_id', $authUser->id)
                            ->where('challenged_id', $user->id);
                    })->orWhere(function ($q2) use ($user, $authUser) {
                        $q2->where('challenger_id', $user->id)
                            ->where('challenged_id', $authUser->id);
                    });
                })->get();

            $h2h = [
                'played' => $h2hMatches->count(),
                'wins' => $h2hMatches->where('winner_id', $authUser->id)->count(),
                'losses' => $h2hMatches->where('winner_id', $user->id)->count(),
            ];
        }

        return Inertia::render('Players/Show', [
            'player' => $user->only(['id', 'name', 'avatar', 'elo_rating', 'matches_played', 'matches_won', 'matches_lost']),
            'matches' => $matches,
            'rank' => $rank,
            'h2h' => $h2h,
        ]);
    }
}
