<?php

namespace App\Services\Player;

use App\Models\User;

class PlayerExportService
{
    public function build(User $user): array
    {
        $player = $user->player;

        $classes = $player->classParticipants()
            ->with('schoolClass')
            ->get()
            ->map(fn($cp) => $this->buildClassData($player, $cp));

        return [
            'profil' => [
                'prenom' => $user->first_name,
                'nom' => $user->last_name,
                'email' => $user->email,
                'photo' => $user->profile_picture,
                'compte_cree_le' => $user->created_at->toDateTimeString(),
            ],
            'code_joueur' => $player->code,
            'classes' => $classes,
            'export_date' => now()->toDateTimeString(),
        ];
    }

    public function summary(User $user): array
    {
        $data    = $this->build($user);
        $classes = collect($data['classes']);

        return [
            'matchCount'      => $classes->sum(fn($c) => count($c['matchs'])),
            'eloHistoryCount' => $classes->sum(fn($c) => count($c['historique_elo'])),
            'profileSize'     => strlen(json_encode($data['profil'])),
            'eloSize'         => strlen(json_encode($classes->pluck('historique_elo'))),
            'matchSize'       => strlen(json_encode($classes->pluck('matchs'))),
        ];
    }

    public function filename(): string
    {
        return 'mybad-donnees-' . now()->format('Y-m-d') . '.json';
    }

    private function buildClassData($player, $cp): array
    {
        $classId = $cp->school_class_id;

        $matchs = $player->gameMatches()
            ->with(['classSession', 'players.user'])
            ->whereHas('classSession', fn($q) => $q->where('school_class_id', $classId))
            ->get()
            ->map(function ($match) use ($player) {
                $opponent = $match->players->firstWhere('id', '!=', $player->id);

                return [
                    'id' => $match->id,
                    'adversaire' => $opponent?->user->full_name,
                    'score' => $match->pivot->score,
                    'score_adversaire' => $opponent?->pivot->score,
                    'date' => $match->created_at->toDateTimeString(),
                ];
            });

        $eloHistories = $cp->eloHistories()
            ->orderBy('created_at')
            ->get(['elo_before', 'elo_after', 'created_at']);

        return [
            'cours'          => $cp->schoolClass->name,
            'elo_actuel'     => (float) $cp->elo_rating,
            'historique_elo' => $eloHistories,
            'matchs'         => $matchs,
        ];
    }
}
