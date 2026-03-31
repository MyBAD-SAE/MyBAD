<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\ClassParticipant;
use App\Models\GameMatch;
use App\Services\Ranking\RankingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly RankingService $rankingService,
    ) {}

    public function index(): Response
    {
        $user = auth('admin')->user();
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

        $playerCount = 0;
        $matchCount = 0;
        $rankingPlayers = [];

        if ($selectedClassId) {
            $playerCount = ClassParticipant::forClass($selectedClassId)
                ->forPlayerType()
                ->count();

            $matchCount = GameMatch::forClass($selectedClassId)->count();

            $rankingPlayers = $this->rankingService->getRankingForClassId($selectedClassId);
        }

        return Inertia::render('Admin/Dashboard', [
            'classes'         => $classes,
            'selectedClassId' => $selectedClassId,
            'playerCount'     => $playerCount,
            'matchCount'      => $matchCount,
            'rankingPlayers'  => $rankingPlayers,
        ]);
    }

    public function selectClass(Request $request): RedirectResponse
    {
        $request->validate(['class_id' => 'required|integer']);

        $adminUser = auth('admin')->user()->adminUser;

        $hasAccess = $adminUser->classParticipants()
            ->whereHas('schoolClass', fn ($q) => $q->where('id', $request->class_id))
            ->exists();

        if (!$hasAccess) {
            abort(403);
        }

        session(['admin_selected_class_id' => (int) $request->class_id]);

        return back();
    }
}
