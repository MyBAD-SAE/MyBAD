<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ClassParticipant;
use App\Models\ClassSession;
use App\Models\GameMatch;
use App\Services\Match\EloService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminMatchesController extends Controller
{
    public function __construct(private readonly EloService $eloService) {}

    /**
     * @OA\Get(
     *     path="/admin/matchs",
     *     tags={"Admin - Matchs"},
     *     summary="Liste des matchs par séance",
     *     operationId="admin.matches.index",
     *     security={{"session":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Props Inertia de la page matchs",
     *         @OA\JsonContent(
     *             @OA\Property(property="sessions", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="date", type="string", format="date"),
     *                 @OA\Property(property="match_count", type="integer"),
     *                 @OA\Property(property="matches", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="number", type="integer"),
     *                     @OA\Property(property="player1", type="object"),
     *                     @OA\Property(property="player2", type="object")
     *                 ))
     *             )),
     *             @OA\Property(property="totalMatchCount", type="integer"),
     *             @OA\Property(property="topMatchesPlayer", type="string", nullable=true),
     *             @OA\Property(property="topWinsPlayer", type="string", nullable=true),
     *             @OA\Property(property="selectedClass", type="object", nullable=true)
     *         )
     *     )
     * )
     */
    public function index(): Response
    {
        $user = Auth::guard('admin')->user();
        $adminUser = $user->adminUser;

        $classIds = $adminUser->classParticipants()->pluck('school_class_id');

        $selectedClassId = session('admin_selected_class_id');

        if (!$selectedClassId || !$classIds->contains($selectedClassId)) {
            $selectedClassId = $classIds->first();
        }

        $selectedClass = $selectedClassId
            ? \App\Models\SchoolClass::find($selectedClassId)
            : null;

        $sessions = [];
        $totalMatchCount = 0;
        $topMatchesPlayer = null;
        $topWinsPlayer = null;

        if ($selectedClassId) {
            $classSessions = ClassSession::forClass($selectedClassId)
                ->with(['gameMatches' => fn ($q) => $q->orderByDesc('created_at'), 'gameMatches.players.user'])
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
                            'id'     => $p1->id,
                            'name'   => $p1->user->full_name,
                            'avatar' => $p1->user->profile_picture,
                            'score'  => $p1->pivot->score,
                            'won'    => $p1Wins,
                        ],
                        'player2' => [
                            'id'     => $p2->id,
                            'name'   => $p2->user->full_name,
                            'avatar' => $p2->user->profile_picture,
                            'score'  => $p2->pivot->score,
                            'won'    => !$p1Wins,
                        ],
                    ];
                }

                if (count($sessionMatches) > 0) {
                    $session->setAttribute('match_count', count($sessionMatches));
                    $session->setAttribute('matches', $sessionMatches);
                    $sessions[] = $session;
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

        return Inertia::render('Admin/Matches', [
            'sessions'         => $sessions,
            'totalMatchCount'  => $totalMatchCount,
            'topMatchesPlayer' => $topMatchesPlayer,
            'topWinsPlayer'    => $topWinsPlayer,
            'selectedClass'    => $selectedClass,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/admin/matchs/{gameMatch}",
     *     tags={"Admin - Matchs"},
     *     summary="Modifier les scores d'un match (recalcule l'ELO)",
     *     operationId="admin.matches.update",
     *     security={{"session":{}}},
     *     @OA\Parameter(name="gameMatch", in="path", required=true, description="ID du match", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"player1_id","player2_id","score1","score2"},
     *             @OA\Property(property="player1_id", type="integer"),
     *             @OA\Property(property="player2_id", type="integer"),
     *             @OA\Property(property="score1", type="integer", minimum=0, maximum=30),
     *             @OA\Property(property="score2", type="integer", minimum=0, maximum=30)
     *         )
     *     ),
     *     @OA\Response(response=302, description="Redirection vers /admin/matchs"),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function update(Request $request, GameMatch $gameMatch): RedirectResponse
    {
        $validated = $request->validate([
            'player1_id' => 'required|exists:players,id',
            'player2_id' => 'required|exists:players,id',
            'score1'     => 'required|integer|min:0|max:30',
            'score2'     => 'required|integer|min:0|max:30',
        ]);

        $schoolClassId = $gameMatch->classSession->school_class_id;

        DB::transaction(function () use ($gameMatch, $validated, $schoolClassId) {
            // Annuler les anciens ELOs
            $this->revertElo($gameMatch, $schoolClassId);

            // Mettre à jour les scores avec les bons IDs
            $gameMatch->players()->updateExistingPivot($validated['player1_id'], ['score' => $validated['score1']]);
            $gameMatch->players()->updateExistingPivot($validated['player2_id'], ['score' => $validated['score2']]);

            // Recalculer et appliquer les nouveaux ELOs
            $eloChange1 = $this->eloService->calculateEloChange(
                $validated['player1_id'],
                $validated['player2_id'],
                $validated['score1'],
                $validated['score2'],
                $schoolClassId,
            );
            $eloChange2 = $this->eloService->calculateEloChange(
                $validated['player2_id'],
                $validated['player1_id'],
                $validated['score2'],
                $validated['score1'],
                $schoolClassId,
            );

            $this->eloService->updateElo($validated['player1_id'], $eloChange1, $schoolClassId, $gameMatch->id);
            $this->eloService->updateElo($validated['player2_id'], $eloChange2, $schoolClassId, $gameMatch->id);
        });

        return redirect()->route('admin.matches')->with('success', 'Match mis à jour avec succès.');
    }

    /**
     * @OA\Delete(
     *     path="/admin/matchs/{gameMatch}",
     *     tags={"Admin - Matchs"},
     *     summary="Supprimer un match (annule les ELO associés)",
     *     operationId="admin.matches.destroy",
     *     security={{"session":{}}},
     *     @OA\Parameter(name="gameMatch", in="path", required=true, description="ID du match", @OA\Schema(type="integer")),
     *     @OA\Response(response=302, description="Redirection vers /admin/matchs")
     * )
     */
    public function destroy(GameMatch $gameMatch): RedirectResponse
    {
        $schoolClassId = $gameMatch->classSession->school_class_id;

        DB::transaction(function () use ($gameMatch, $schoolClassId) {
            $this->revertElo($gameMatch, $schoolClassId);
            $gameMatch->players()->detach();
            $gameMatch->delete();
        });

        return redirect()->route('admin.matches')->with('success', 'Match supprimé avec succès.');
    }

    private function revertElo(GameMatch $gameMatch, int $schoolClassId): void
    {
        foreach ($gameMatch->players as $player) {
            $participation = ClassParticipant::forClass($schoolClassId)
                ->forPlayer($player->id)
                ->first();

            if (!$participation) {
                continue;
            }

            $history = $participation->eloHistories()
                ->where('game_match_id', $gameMatch->id)
                ->first();

            if (!$history) {
                continue;
            }

            $participation->update(['elo_rating' => $history->elo_before]);
            $history->delete();
        }
    }
}
