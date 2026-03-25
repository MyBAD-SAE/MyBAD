<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassParticipantResource;
use App\Models\GameMatch;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class EloDetailsController extends Controller
{
    public function index(): Response
    {
        $user = Auth::guard('player')->user();
        $player = $user->player;

        $participation = $player?->selectedParticipation()?->load('participantable.user');
        $classId = $participation?->school_class_id;

        $classes = $player
            ? $player->classParticipants()->with('schoolClass')->get()
                ->map(fn ($cp) => ['id' => $cp->school_class_id, 'name' => $cp->schoolClass->name])
                ->values()->all()
            : [];

        $currentElo = (float) ($participation?->elo_rating ?? 0);
        $bestElo = $currentElo;
        $eloHistory = [];
        $eloWeekDiff = 0;
        $rank = null;
        $totalPlayers = 0;

        if ($participation) {
            // Full ELO history
            $histories = $participation->eloHistories()->oldest()->get();

            if ($histories->isNotEmpty()) {
                $eloHistory = $histories->map(fn ($h) => [
                    'elo' => (float) $h->elo_after,
                    'date' => $h->created_at->format('d/m'),
                ])->prepend([
                    'elo' => (float) $histories->first()->elo_before,
                    'date' => $histories->first()->created_at->format('d/m'),
                ])->values()->all();

                $bestElo = max($currentElo, $histories->max('elo_after'), $histories->max('elo_before'));

                // Weekly diff (last 7 days)
                $weekAgo = now()->subDays(7);
                $recentHistories = $histories->filter(fn ($h) => $h->created_at->gte($weekAgo));
                if ($recentHistories->isNotEmpty()) {
                    $eloWeekDiff = round($currentElo - (float) $recentHistories->first()->elo_before, 1);
                }
            }

            // Rank among class participants
            $totalPlayers = \App\Models\ClassParticipant::where('school_class_id', $classId)
                ->where('participantable_type', \App\Models\Player::class)
                ->count();

            $rank = \App\Models\ClassParticipant::where('school_class_id', $classId)
                ->where('participantable_type', \App\Models\Player::class)
                ->where('elo_rating', '>', $participation->elo_rating)
                ->count() + 1;
        }

        return Inertia::render('Player/EloDetails', [
            'currentElo' => $currentElo,
            'bestElo' => round($bestElo, 1),
            'eloWeekDiff' => $eloWeekDiff,
            'eloHistory' => $eloHistory,
            'rank' => $rank,
            'totalPlayers' => $totalPlayers,
            'classes' => $classes,
            'selectedClassId' => $classId,
        ]);
    }
}
