<?php

namespace App\Services\Dashboard;

use App\Http\Resources\ClassParticipantResource;
use App\Models\ClassParticipant;
use App\Models\GameMatch;
use App\Models\Player;
use App\Models\User;
use App\Services\Player\MatchHistoryService;
use App\Services\Player\PlayerProfileService;
use Carbon\Carbon;

class DashboardService
{
    public function __construct(
        private readonly MatchHistoryService $matchHistoryService,
        private readonly PlayerProfileService $profileService,
    ) {}

    public function getDashboardData(User $user): array
    {
        $player        = $user->player;
        $participation = $player?->selectedParticipation()?->load('participantable.user');
        $classId       = $participation?->school_class_id;

        $classes       = $this->profileService->getPlayerClasses($player);
        $eloDiff       = 0;
        $eloHistory    = [];
        $matchStats    = ['wins' => 0, 'losses' => 0, 'total' => 0, 'sessions' => []];
        $totalMatches  = 0;
        $winStreak     = 0;
        $recentMatches = [];

        if ($player) {
            $matchData     = $this->getMatchStats($player, $classId);
            $matchStats    = $matchData['matchStats'];
            $totalMatches  = $matchData['totalMatches'];
            $recentMatches = $this->getRecentMatches($player, $classId);
            $winStreak     = $this->getWinStreak($player, $matchData['matches']);

            if ($participation) {
                $eloData    = $this->getEloData($participation);
                $eloDiff    = $eloData['eloDiff'];
                $eloHistory = $eloData['eloHistory'];
            }
        }

        return [
            'participant'     => $participation ? ClassParticipantResource::make($participation)->resolve() : null,
            'classes'         => $classes,
            'selectedClassId' => $classId,
            'playerCode'      => $player?->code,
            'firstName'       => $user->first_name,
            'avatarUrl'       => $user->profile_picture,
            'eloDiff'         => $eloDiff,
            'eloHistory'      => $eloHistory,
            'matchStats'      => $matchStats,
            'totalMatches'    => $totalMatches,
            'winStreak'       => $winStreak,
            'recentMatches'   => $recentMatches,
        ];
    }

    /**
     * Récupère les stats de matchs groupées par séance (4 dernières séances).
     */
    public function getMatchStats(Player $player, ?int $classId): array
    {
        $matches = $this->getPlayerMatches($player, $classId);

        $bySession = [];
        $totalMatches = 0;

        foreach ($matches as $match) {
            $myScore = $match->players->firstWhere('id', $player->id)->pivot->score;
            $oppScore = $match->players->where('id', '!=', $player->id)->first()->pivot->score;
            $won = $myScore > $oppScore;
            $totalMatches++;

            $sessionId = $match->class_session_id;
            if (!isset($bySession[$sessionId])) {
                $date = $match->classSession?->date;
                $bySession[$sessionId] = [
                    'wins'     => 0,
                    'losses'   => 0,
                    'raw_date' => $date,
                    'date'     => $date ? Carbon::parse($date)->format('d/m') : null,
                ];
            }

            $won ? $bySession[$sessionId]['wins']++ : $bySession[$sessionId]['losses']++;
        }

        usort($bySession, fn ($a, $b) => ($a['raw_date'] ?? '') <=> ($b['raw_date'] ?? ''));

        $last4Sessions = array_slice($bySession, -4);

        $stats = ['wins' => 0, 'losses' => 0, 'total' => 0, 'sessions' => []];
        foreach ($last4Sessions as $s) {
            $stats['wins'] += $s['wins'];
            $stats['losses'] += $s['losses'];
            $stats['total'] += $s['wins'] + $s['losses'];
        }
        $stats['sessions'] = collect($last4Sessions)->map(fn ($s) => [
            'wins'   => $s['wins'],
            'losses' => $s['losses'],
            'date'   => $s['date'],
        ])->values()->all();

        return [
            'matchStats'   => $stats,
            'totalMatches' => $totalMatches,
            'matches'      => $matches,
        ];
    }

    /**
     * Calcule l'historique ELO complet et la différence globale.
     */
    public function getEloData(ClassParticipant $participant): array
    {
        $history = $participant->eloHistories()->oldest()->get();

        if ($history->isEmpty()) {
            return ['eloDiff' => 0, 'eloHistory' => []];
        }

        return [
            'eloDiff'    => round((float) $participant->elo_rating - (float) $history->first()->elo_before, 2),
            'eloHistory' => $history->map(fn ($h) => [
                'elo'  => (float) $h->elo_after,
                'date' => $h->created_at->format('d/m'),
            ])->prepend([
                'elo'  => (float) $history->first()->elo_before,
                'date' => $history->first()->created_at->format('d/m'),
            ])->values()->all(),
        ];
    }

    /**
     * Calcule la série de victoires consécutives en cours.
     */
    public function getWinStreak(Player $player, $matches): int
    {
        $sorted = $matches->sortByDesc(
            fn (GameMatch $m) => ($m->classSession?->date ?? '') . '_' . str_pad($m->id, 10, '0', STR_PAD_LEFT)
        );

        $streak = 0;
        foreach ($sorted as $match) {
            $myScore = $match->players->firstWhere('id', $player->id)->pivot->score;
            $oppScore = $match->players->where('id', '!=', $player->id)->first()->pivot->score;

            if ($myScore > $oppScore) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Retourne les 5 derniers matchs du joueur formatés pour le widget du dashboard.
     */
    public function getRecentMatches(Player $player, ?int $classId): array
    {
        return $this->matchHistoryService->getMatches($player, $classId, 5);
    }

    /**
     * Récupère tous les matchs d'un joueur pour une classe donnée.
     */
    private function getPlayerMatches(Player $player, ?int $classId)
    {
        return GameMatch::forPlayer($player->id)
            ->when($classId, fn ($q) => $q->forClass($classId))
            ->with(['players', 'classSession'])
            ->get();
    }
}
