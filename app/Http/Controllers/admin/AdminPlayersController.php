<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\ClassParticipant;
use App\Models\GameMatch;
use App\Models\EloHistory;
use App\Models\Player;
use App\Models\User;
use App\Services\Ranking\RankingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminPlayersController extends Controller
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
            ->map(fn($cp) => [
                'id'   => $cp->schoolClass->id,
                'name' => $cp->schoolClass->name,
            ])
            ->values()
            ->all();

        $selectedClassId = session('admin_selected_class_id');

        if (!$selectedClassId || !collect($classes)->contains('id', $selectedClassId)) {
            $selectedClassId = $classes[0]['id'] ?? null;
        }

        $players = [];
        $playerCount = 0;
        $activePlayerCount = 0;
        $matchCount = 0;
        $averageElo = 0;

        if ($selectedClassId) {
            $players = $this->rankingService->getRankingForClassId($selectedClassId);
            $playerCount = count($players);
            $activePlayerCount = count(array_filter($players, fn ($p) => $p['isActive']));
            $matchCount = GameMatch::forClass($selectedClassId)->count();

            if ($playerCount > 0) {
                $averageElo = round(
                    ClassParticipant::forClass($selectedClassId)
                        ->forPlayerType()
                        ->avg('elo_rating'),
                    1
                );
            }
        }

        return Inertia::render('Admin/Players', [
            'players'           => $players,
            'playerCount'       => $playerCount,
            'activePlayerCount' => $activePlayerCount,
            'matchCount'        => $matchCount,
            'averageElo'        => $averageElo,
            'classes'           => $classes,
            'selectedClassId'   => $selectedClassId,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
            'elo'  => 'required|numeric|min:0',
        ]);

        $player = Player::where('code', $validated['code'])->first();

        if (!$player) {
            return redirect()->route('admin.players')->withErrors(['code' => 'Aucun joueur trouvé avec ce code.']);
        }

        $user = auth('admin')->user();
        $adminUser = $user->adminUser;

        $classIds = $adminUser->classParticipants()->pluck('school_class_id');
        $selectedClassId = session('admin_selected_class_id');

        if (!$selectedClassId || !$classIds->contains($selectedClassId)) {
            $selectedClassId = $classIds->first();
        }

        if (!$selectedClassId) {
            return redirect()->route('admin.players');
        }

        $exists = ClassParticipant::forClass($selectedClassId)
            ->where('participantable_type', Player::class)
            ->where('participantable_id', $player->id)
            ->exists();

        if ($exists) {
            return redirect()->route('admin.players')->withErrors(['code' => 'Ce joueur est déjà dans la classe.']);
        }

        ClassParticipant::create([
            'participantable_type' => Player::class,
            'participantable_id'   => $player->id,
            'elo_rating'           => $validated['elo'],
            'school_class_id'      => $selectedClassId,
        ]);

        return redirect()->route('admin.players')->with('success', 'Joueur ajouté avec succès.');
    }

    public function update(Request $request, ClassParticipant $participant): RedirectResponse
    {
        $validated = $request->validate([
            'elo'        => 'required|numeric|min:0',
            'is_active'  => 'required|boolean',
            'make_admin' => 'sometimes|boolean',
        ]);

        $eloBefore = (float) $participant->elo_rating;
        $eloAfter  = (float) $validated['elo'];

        $participant->update(['elo_rating' => $eloAfter]);

        if ($eloBefore !== $eloAfter) {
            EloHistory::create([
                'participant_id' => $participant->id,
                'game_match_id'  => null,
                'elo_before'     => $eloBefore,
                'elo_after'      => $eloAfter,
            ]);
        }

        $user = User::find($request->input('user_id'));

        if ($user) {
            $user->update(['is_active' => $validated['is_active']]);

            if ($request->boolean('make_admin')) {
                $admin = $user->adminUser;

                if (!$admin) {
                    $admin = AdminUser::create([
                        'id' => Str::uuid()->toString(),
                        'user_id' => $user->id,
                    ]);
                }

                $alreadyInClass = ClassParticipant::where('participantable_type', AdminUser::class)
                    ->where('participantable_id', $admin->id)
                    ->where('school_class_id', $participant->school_class_id)
                    ->exists();

                if (!$alreadyInClass) {
                    ClassParticipant::create([
                        'participantable_type' => AdminUser::class,
                        'participantable_id'   => $admin->id,
                        'elo_rating'           => null,
                        'school_class_id'      => $participant->school_class_id,
                    ]);
                }
            } elseif (!$request->boolean('make_admin') && $user->adminUser) {
                ClassParticipant::where('participantable_type', AdminUser::class)
                    ->where('participantable_id', $user->adminUser->id)
                    ->where('school_class_id', $participant->school_class_id)
                    ->delete();

                $remainingParticipations = ClassParticipant::where('participantable_type', AdminUser::class)
                    ->where('participantable_id', $user->adminUser->id)
                    ->exists();

                if (!$remainingParticipations) {
                    $user->adminUser->delete();
                }
            }
        }

        return redirect()->route('admin.players')->with('success', 'Joueur mis à jour avec succès.');
    }

    public function destroy(ClassParticipant $participant): RedirectResponse
    {
        $participant->eloHistories()->delete();
        $participant->delete();

        return redirect()->route('admin.players')->with('success', 'Joueur supprimé avec succès.');
    }
}
