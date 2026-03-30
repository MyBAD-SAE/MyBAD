<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\GameMatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminMatchsController extends Controller
{
    public function index(): Response
    {
        $user = Auth::guard('admin')->user();
        $adminUser = $user->adminUser;

        $classes = $adminUser->classParticipants()
            ->with('schoolClass')
            ->get()
            ->map(fn ($cp) => [
                'id'   => $cp->schoolClass->id,
                'name' => $cp->schoolClass->name,
            ])
            ->values()
            ->all();

        $selectedClassId = session('admin_selected_class_id');

        if (!$selectedClassId || !collect($classes)->contains('id', $selectedClassId)) {
            $selectedClassId = $classes[0]['id'] ?? null;
        }

        $sessions = [];
        $totalMatchCount = 0;
        $topMatchesPlayer = null;
        $topWinsPlayer = null;

        if ($selectedClassId) {
            $classSessions = ClassSession::forClass($selectedClassId)
                ->with(['gameMatches.players.user'])
                ->orderByDesc('date')
                ->get();

            $playerStats = [];

            foreach ($classSessions as $session) {
                $sessionMatches = [];

                foreach ($session->gameMatches as $index => $match) {
                    if ($match->players->count() !== 2) {
                        continue;
                    }

                    $p1 = $match->players->first();
                    $p2 = $match->players->last();

                    $p1Wins = $p1->pivot->score > $p2->pivot->score;

                    foreach ([$p1, $p2] as $p) {
                        $pid = $p->id;
                        if (!isset($playerStats[$pid])) {
                            $playerStats[$pid] = [
                                'name' => $p->user->first_name . ' ' . mb_substr($p->user->last_name, 0, 1) . '.',
                                'matches' => 0,
                                'wins' => 0,
                            ];
                        }
                        $playerStats[$pid]['matches']++;
                    }

                    if ($p1Wins) {
                        $playerStats[$p1->id]['wins']++;
                    } else {
                        $playerStats[$p2->id]['wins']++;
                    }

                    $sessionMatches[] = [
                        'id' => $match->id,
                        'number' => $index + 1,
                        'date' => $session->date->format('d M.'),
                        'player1' => [
                            'name'   => $p1->user->full_name,
                            'avatar' => $p1->user->profile_picture,
                            'score'  => $p1->pivot->score,
                            'won'    => $p1Wins,
                        ],
                        'player2' => [
                            'name'   => $p2->user->full_name,
                            'avatar' => $p2->user->profile_picture,
                            'score'  => $p2->pivot->score,
                            'won'    => !$p1Wins,
                        ],
                    ];
                }

                if (count($sessionMatches) > 0) {
                    $sessions[] = [
                        'id'         => $session->id,
                        'label'      => 'Séance du ' . $session->date->translatedFormat('j F'),
                        'date'       => $session->date->translatedFormat('j M. Y'),
                        'matchCount' => count($sessionMatches),
                        'matches'    => $sessionMatches,
                    ];
                }

                $totalMatchCount += count($sessionMatches);
            }

            if (!empty($playerStats)) {
                $topMatches = collect($playerStats)->sortByDesc('matches')->first();
                $topWins = collect($playerStats)->sortByDesc('wins')->first();
                $topMatchesPlayer = $topMatches['name'];
                $topWinsPlayer = $topWins['name'];
            }
        }

        return Inertia::render('Admin/Matchs', [
            'sessions'         => $sessions,
            'totalMatchCount'  => $totalMatchCount,
            'topMatchesPlayer' => $topMatchesPlayer,
            'topWinsPlayer'    => $topWinsPlayer,
            'classes'          => $classes,
            'selectedClassId'  => $selectedClassId,
        ]);
    }

    public function update(Request $request, GameMatch $gameMatch): RedirectResponse
    {
        $validated = $request->validate([
            'score1' => 'required|integer|min:0|max:30',
            'score2' => 'required|integer|min:0|max:30',
        ]);

        $players = $gameMatch->players()->get();

        if ($players->count() === 2) {
            $gameMatch->players()->updateExistingPivot($players->first()->id, ['score' => $validated['score1']]);
            $gameMatch->players()->updateExistingPivot($players->last()->id, ['score' => $validated['score2']]);
        }

        return redirect()->route('admin.matchs');
    }

    public function destroy(GameMatch $gameMatch): RedirectResponse
    {
        $gameMatch->players()->detach();
        $gameMatch->delete();

        return redirect()->route('admin.matchs');
    }
}
