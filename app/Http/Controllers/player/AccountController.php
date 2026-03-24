<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Player\UpdateProfileRequest;
use App\Http\Resources\ClassParticipantResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Player\PlayerExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function index(): Response
    {
        $user = auth('player')->user();
        $player = $user->player;
        $classId = $player?->selectedParticipation()?->school_class_id;

        return Inertia::render('Player/Profil', [
            'participant' => $this->getParticipantWithRank($user, $classId),
            'classes' => $this->getAllClasses($player),
            'selectedClassId' => $classId,
            'matchCount' => $player?->gameMatches()->count() ?? 0,
        ]);
    }

    public function infos(): Response
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        return Inertia::render('Player/InfosPersonnelles', [
            'user' => UserResource::make($user)->resolve(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        if ($request->filled('new_pin')) {
            if (!Hash::check($request->current_pin, $user->player->pin ?? '')) {
                return back()->withErrors(['current_pin' => 'Le code PIN actuel est incorrect.']);
            }
            $user->player->update(['pin' => $request->new_pin]);
        }

        if ($request->filled('new_password')) {
            $user->update(['password' => Hash::make($request->new_password)]);
        }

        return redirect()->route('player.account.infos');
    }

    public function confidentialite(PlayerExportService $export): Response
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        return Inertia::render('Player/Confidentialite', $export->summary($user));
    }

    public function download(PlayerExportService $export): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        return response()->json($export->build($user), 200, [
            'Content-Disposition' => 'attachment; filename="' . $export->filename() . '"',
        ]);
    }

    public function selectClass(Request $request): RedirectResponse
    {
        $request->validate(['class_id' => 'required|integer']);

        $player = auth('player')->user()->player;
        $classIds = $player->classParticipants()->pluck('school_class_id');

        if ($classIds->contains($request->class_id)) {
            session(['selected_class_id' => (int)$request->class_id]);
        }

        return redirect()->back();
    }

    private function getParticipantWithRank($user, ?int $classId = null): ?array
    {
        $query = $user->player?->classParticipants()->with('participantable.user');

        if ($classId) {
            $query = $query->where('school_class_id', $classId);
        }

        $participant = $query?->selectRaw('*, (
                SELECT COUNT(*) + 1
                FROM class_participants cp
                WHERE cp.school_class_id = class_participants.school_class_id
                  AND cp.elo_rating > class_participants.elo_rating
            ) as `rank`')
            ->first();

        return $participant ? ClassParticipantResource::make($participant)->resolve() : null;
    }

    private function getAllClasses($player): array
    {
        if (!$player) {
            return [];
        }

        return $player->classParticipants()
            ->with('schoolClass')
            ->get()
            ->map(fn($cp) => ['id' => $cp->school_class_id, 'name' => $cp->schoolClass->name])
            ->values()
            ->all();
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'confirmation' => ['required', 'in:SUPPRIMER'],
        ]);

        /** @var User $user */
        $user = Auth::guard('player')->user();

        $user->delete();

        Auth::guard('player')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('player.login');
    }
}
