<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Player\UpdateProfileRequest;
use App\Http\Resources\ClassParticipantResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function index(): Response
    {
        $user = auth('player')->user();

        return Inertia::render('Player/Profil', [
            'participant' => $this->getParticipantWithRank($user),
            'user' => [
                'first_name'      => $user->first_name,
                'last_name'       => $user->last_name,
                'email'           => $user->email,
                'profile_picture' => $user->profile_picture,
            ],
            'playerCode' => $user->player?->code,
        ]);
    }

    public function infos(): Response
    {
        return Inertia::render('Player/InfosPersonnelles', [
            'participant' => $this->getParticipantWithRank(auth('player')->user()),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
        ]);

        if ($request->filled('new_pin')) {
            if (! Hash::check($request->current_pin, $user->player->pin ?? '')) {
                return back()->withErrors(['current_pin' => 'Le code PIN actuel est incorrect.']);
            }
            $user->player->update(['pin' => $request->new_pin]);
        }

        if ($request->filled('new_password')) {
            $user->update(['password' => Hash::make($request->new_password)]);
        }

        return redirect()->route('player.account.infos');
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'mimes:jpg,jpeg,png,webp'],
        ], [
            'photo.required' => 'Veuillez sélectionner une photo.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'L\'image doit être au format JPG, PNG ou WebP.',
        ]);

        /** @var User $user */
        $user = Auth::guard('player')->user();

        // Delete old photo from storage if it was a local upload
        if ($user->profile_picture && str_starts_with($user->profile_picture, '/storage/profile-photos/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $user->profile_picture));
        }

        Storage::disk('public')->makeDirectory('profile-photos');

        // Convertir l'image en WebP
        $file = $request->file('photo');
        $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
        $filename = 'profile-photos/' . uniqid() . '.webp';
        $fullPath = storage_path('app/public/' . $filename);
        imagewebp($image, $fullPath, 80);

        $user->update([
            'profile_picture' => '/storage/' . $filename,
        ]);

        return back();
    }

    public function confidentialite(): \Inertia\Response
    {
        $user = auth('player')->user();
        $player = $user->player;

        $profileData = json_encode([
            'prenom' => $user->first_name,
            'nom' => $user->last_name,
            'email' => $user->email,
            'photo' => $user->profile_picture,
            'compte_cree_le' => $user->created_at,
        ]);

        $eloData = json_encode($player->eloHistories()->get(['elo_before', 'elo_after', 'created_at']));
        $matchData = json_encode($player->gameMatches()->get());

        return Inertia::render('Player/Confidentialite', [
            'matchCount' => $player->gameMatches()->count(),
            'eloHistoryCount' => $player->eloHistories()->count(),
            'profileSize' => strlen($profileData),
            'eloSize' => strlen($eloData),
            'matchSize' => strlen($matchData),
        ]);
    }

    public function download(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();
        $player = $user->player;

        $data = [
            'profil' => [
                'prenom' => $user->first_name,
                'nom' => $user->last_name,
                'email' => $user->email,
                'photo' => $user->profile_picture,
                'compte_cree_le' => $user->created_at->toDateTimeString(),
            ],
            'statistiques' => [
                'code_joueur' => $player->code,
                'historique_elo' => $player->eloHistories()
                    ->orderBy('created_at')
                    ->get(['elo_before', 'elo_after', 'created_at']),
            ],
            'matchs' => $player->gameMatches()
                ->with('classSession')
                ->get()
                ->map(fn ($match) => [
                    'id' => $match->id,
                    'session_id' => $match->class_session_id,
                    'score' => $match->pivot->score,
                    'validated' => $match->pivot->validated,
                    'date' => $match->created_at->toDateTimeString(),
                ]),
            'export_date' => now()->toDateTimeString(),
        ];

        $fileName = 'mybad-donnees-' . now()->format('Y-m-d') . '.json';

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    private function getParticipantWithRank($user): ?array
    {
        $participant = $user->player
            ?->classParticipants()
            ->with('participantable.user')
            ->selectRaw('*, (
                SELECT COUNT(*) + 1
                FROM class_participants cp
                WHERE cp.school_class_id = class_participants.school_class_id
                  AND cp.elo_rating > class_participants.elo_rating
            ) as `rank`')
            ->first();

        return $participant ? ClassParticipantResource::make($participant)->resolve() : null;
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
