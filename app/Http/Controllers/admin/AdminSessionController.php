<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ClassParticipant;
use App\Models\ClassSession;
use App\Models\GameMatch;
use App\Models\PublicView;
use App\Services\Ranking\RankingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminSessionController extends Controller
{
    public function __construct(
        private readonly RankingService $rankingService,
    ) {}

    public function show(): Response|RedirectResponse
    {
        $adminUser = auth('admin')->user()->adminUser;
        $selectedClassId = session('admin_selected_class_id');

        $classIds = $adminUser->classParticipants()->pluck('school_class_id');

        if (!$selectedClassId || !$classIds->contains($selectedClassId)) {
            $selectedClassId = $classIds->first();
        }

        if (!$selectedClassId) {
            return redirect()->route('admin.dashboard');
        }

        $session = ClassSession::forClass($selectedClassId)->active()->first();

        if (!$session) {
            return redirect()->route('admin.dashboard');
        }

        $publicView = PublicView::where('school_class_id', $selectedClassId)->first();

        $rankingPlayers = $this->rankingService->getRankingForClassId($selectedClassId);

        $playerCount = ClassParticipant::forClass($selectedClassId)
            ->forPlayerType()
            ->count();

        $recentMatches = GameMatch::where('class_session_id', $session->id)
            ->with('players.user')
            ->latest()
            ->take(10)
            ->get()
            ->map(function (GameMatch $match) {
                $players = $match->players;
                if ($players->count() !== 2) {
                    return null;
                }

                $a = $players->first();
                $b = $players->last();

                $winnerIndex = $a->pivot->score > $b->pivot->score ? 0 : ($b->pivot->score > $a->pivot->score ? 1 : null);

                return [
                    'id' => $match->id,
                    'player1' => [
                        'name' => $a->user->first_name,
                        'score' => $a->pivot->score,
                    ],
                    'player2' => [
                        'name' => $b->user->first_name,
                        'score' => $b->pivot->score,
                    ],
                    'winnerIndex' => $winnerIndex,
                    'timeAgo' => $match->created_at->diffForHumans(),
                ];
            })
            ->filter()
            ->values()
            ->all();

        $dayName = 'Séance du ' . $session->date->translatedFormat('l');

        return Inertia::render('Admin/Session', [
            'session' => [
                'id' => $session->id,
                'dayName' => $dayName,
                'fullDate' => ucfirst($session->date->translatedFormat('l j F Y')),
                'isActive' => $session->is_active,
            ],
            'publicLink' => $publicView?->access_token,
            'rankingPlayers' => $rankingPlayers,
            'playerCount' => $playerCount,
            'recentMatches' => $recentMatches,
        ]);
    }

    public function store(): RedirectResponse
    {
        $adminUser = auth('admin')->user()->adminUser;
        $selectedClassId = session('admin_selected_class_id');

        $classIds = $adminUser->classParticipants()->pluck('school_class_id');

        if (!$selectedClassId || !$classIds->contains($selectedClassId)) {
            $selectedClassId = $classIds->first();
        }

        if (!$selectedClassId) {
            return redirect()->route('admin.dashboard');
        }

        // Si une séance est déjà active, rediriger vers elle
        if (ClassSession::forClass($selectedClassId)->active()->exists()) {
            return redirect()->route('admin.session');
        }

        // Clôturer les séances actives existantes (sécurité)
        ClassSession::forClass($selectedClassId)->active()->update(['is_active' => false]);

        // Créer la nouvelle séance
        ClassSession::create([
            'school_class_id' => $selectedClassId,
            'date' => now(),
            'is_active' => true,
        ]);

        // S'assurer qu'un lien public existe
        PublicView::firstOrCreate(
            ['school_class_id' => $selectedClassId],
            ['access_token' => Str::random(32)],
        );

        return redirect()->route('admin.session');
    }

    public function close(): RedirectResponse
    {
        $adminUser = auth('admin')->user()->adminUser;
        $selectedClassId = session('admin_selected_class_id');

        $classIds = $adminUser->classParticipants()->pluck('school_class_id');

        if (!$selectedClassId || !$classIds->contains($selectedClassId)) {
            $selectedClassId = $classIds->first();
        }

        if ($selectedClassId) {
            ClassSession::forClass($selectedClassId)->active()->update(['is_active' => false]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Séance clôturée avec succès.');
    }
}
