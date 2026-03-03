<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMatchRequest;
use App\Http\Requests\UpdateMatchScoreRequest;
use App\Models\GameMatch;
use App\Models\User;
use App\Services\EloService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MatchController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $status = $request->get('status');

        $matches = GameMatch::where(function ($q) use ($user) {
                $q->where('challenger_id', $user->id)
                  ->orWhere('challenged_id', $user->id);
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->with(['challenger', 'challenged', 'winner'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Matches/Index', [
            'matches' => $matches,
            'currentStatus' => $status,
        ]);
    }

    public function create()
    {
        $players = User::where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name', 'elo_rating', 'avatar']);

        return Inertia::render('Matches/Create', [
            'players' => $players,
        ]);
    }

    public function store(StoreMatchRequest $request)
    {
        GameMatch::create([
            'challenger_id' => $request->user()->id,
            'challenged_id' => $request->validated('challenged_id'),
            'status' => 'pending',
        ]);

        return redirect()->route('matches.index')
            ->with('success', 'Challenge sent!');
    }

    public function show(GameMatch $match)
    {
        $match->load(['challenger', 'challenged', 'winner']);

        return Inertia::render('Matches/Show', [
            'match' => $match,
        ]);
    }

    public function update(UpdateMatchScoreRequest $request, GameMatch $match)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($match, $validated) {
            $match->update([
                'challenger_score_set1' => $validated['challenger_score_set1'],
                'challenged_score_set1' => $validated['challenged_score_set1'],
                'challenger_score_set2' => $validated['challenger_score_set2'],
                'challenged_score_set2' => $validated['challenged_score_set2'],
                'challenger_score_set3' => $validated['challenger_score_set3'] ?? null,
                'challenged_score_set3' => $validated['challenged_score_set3'] ?? null,
            ]);

            $challengerSets = $match->setsWonByChallenger();
            $challengedSets = $match->setsWonByChallenged();

            $winnerId = $challengerSets > $challengedSets
                ? $match->challenger_id
                : $match->challenged_id;

            $winner = User::find($winnerId);
            $loser = User::find($winnerId === $match->challenger_id ? $match->challenged_id : $match->challenger_id);

            $elo = new EloService();
            $result = $elo->calculate($winner->elo_rating, $loser->elo_rating);

            $winner->update([
                'elo_rating' => $result['winner_new_rating'],
                'matches_played' => $winner->matches_played + 1,
                'matches_won' => $winner->matches_won + 1,
            ]);

            $loser->update([
                'elo_rating' => $result['loser_new_rating'],
                'matches_played' => $loser->matches_played + 1,
                'matches_lost' => $loser->matches_lost + 1,
            ]);

            $match->update([
                'winner_id' => $winnerId,
                'status' => 'completed',
                'played_at' => now(),
                'elo_change' => $result['change'],
            ]);
        });

        return redirect()->route('matches.show', $match)
            ->with('success', 'Scores submitted! ELO updated.');
    }

    public function accept(GameMatch $match)
    {
        $this->authorize('accept', $match);

        $match->update(['status' => 'accepted']);

        return back()->with('success', 'Challenge accepted!');
    }

    public function decline(GameMatch $match)
    {
        $this->authorize('decline', $match);

        $match->update(['status' => 'declined']);

        return back()->with('success', 'Challenge declined.');
    }

    public function cancel(GameMatch $match)
    {
        $this->authorize('cancel', $match);

        $match->update(['status' => 'cancelled']);

        return back()->with('success', 'Challenge cancelled.');
    }
}
