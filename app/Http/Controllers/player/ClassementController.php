<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Models\ClassParticipant;
use App\Models\Player;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ClassementController extends Controller
{
    public function index(): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('player')->user();
        $player = $user->player;

        // Find the player's school class
        $participation = ClassParticipant::where('participantable_type', Player::class)
            ->where('participantable_id', $player->id)
            ->first();

        if (! $participation) {
            return Inertia::render('Player/Classements', ['players' => []]);
        }

        $schoolClassId = $participation->school_class_id;

        // All participants in this class, ordered by elo
        $participants = ClassParticipant::where('school_class_id', $schoolClassId)
            ->where('participantable_type', Player::class)
            ->with('participantable.user')
            ->orderByDesc('elo_rating')
            ->get();

        // Preload match stats for all players in one query
        $playerIds = $participants->pluck('participantable_id');
        $matchStats = $this->getBulkMatchStats($playerIds, $schoolClassId);
        $eloTrends = $this->getBulkEloTrends($playerIds);

        $players = $participants->values()->map(function ($participant, $index) use ($matchStats, $eloTrends) {
            $playerId = $participant->participantable_id;
            $playerUser = $participant->participantable->user;

            $wins = $matchStats[$playerId]['wins'] ?? 0;
            $losses = $matchStats[$playerId]['losses'] ?? 0;
            $total = $wins + $losses;
            $winRate = $total > 0 ? round(($wins / $total) * 100) : 0;

            return [
                'rank' => $index + 1,
                'name' => $playerUser->full_name,
                'avatar' => $playerUser->profile_picture,
                'elo' => (float) $participant->elo_rating,
                'wins' => $wins,
                'losses' => $losses,
                'trend' => $eloTrends[$playerId] ?? 0,
                'winRate' => $winRate,
            ];
        });

        return Inertia::render('Player/Classements', [
            'players' => $players,
        ]);
    }

    /**
     * Get wins/losses for all players in a school class efficiently.
     */
    private function getBulkMatchStats($playerIds, int $schoolClassId): array
    {
        // Get all match_player entries for these players in this school class
        $entries = DB::table('match_player as mp')
            ->join('game_matches as gm', 'mp.game_match_id', '=', 'gm.id')
            ->join('class_sessions as cs', 'gm.class_session_id', '=', 'cs.id')
            ->whereIn('mp.player_id', $playerIds)
            ->where('cs.school_class_id', $schoolClassId)
            ->select('mp.game_match_id', 'mp.player_id', 'mp.score')
            ->get();

        // Group by match to compare scores
        $matchGroups = $entries->groupBy('game_match_id');

        $stats = [];
        foreach ($playerIds as $id) {
            $stats[$id] = ['wins' => 0, 'losses' => 0];
        }

        foreach ($matchGroups as $matchEntries) {
            if ($matchEntries->count() !== 2) {
                continue;
            }

            $a = $matchEntries->first();
            $b = $matchEntries->last();

            if ($a->score > $b->score) {
                $stats[$a->player_id]['wins']++;
                $stats[$b->player_id]['losses']++;
            } elseif ($b->score > $a->score) {
                $stats[$b->player_id]['wins']++;
                $stats[$a->player_id]['losses']++;
            }
        }

        return $stats;
    }

    /**
     * Get Elo trend (total change from all history) for each player.
     */
    private function getBulkEloTrends($playerIds): array
    {
        return DB::table('elo_histories')
            ->whereIn('player_id', $playerIds)
            ->groupBy('player_id')
            ->selectRaw('player_id, ROUND(SUM(elo_after - elo_before), 1) as trend')
            ->pluck('trend', 'player_id')
            ->map(fn ($v) => (float) $v)
            ->toArray();
    }
}
