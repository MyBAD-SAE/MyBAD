<?php

namespace App\Services\Player;

use App\Http\Resources\ClassParticipantResource;
use App\Http\Resources\PlayerResource;
use App\Models\ClassParticipant;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PlayerProfileService
{
    public function getProfileData(User $user): array
    {
        $player  = $user->player;
        $classId = $player?->selectedParticipation()?->school_class_id;

        $participant = $this->getParticipantWithRank($user, $classId);

        return [
            'participant'     => $participant,
            'player'          => $participant === null && $player
                ? PlayerResource::make($player->load('user'))->resolve()
                : null,
            'classes'         => $this->getPlayerClasses($player),
            'selectedClassId' => $classId,
            'matchCount'      => $player?->gameMatches()->count() ?? 0,
        ];
    }

    public function updateProfile(User $user, array $data): void
    {
        $user->update([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
        ]);

        if (!empty($data['new_pin'] ?? null)) {
            $user->player->update(['pin' => $data['new_pin']]);
        }

        if (!empty($data['new_password'] ?? null)) {
            $user->update(['password' => Hash::make($data['new_password'])]);
        }
    }

    public function updatePhoto(User $user, UploadedFile $file): void
    {
        if ($user->profile_picture && str_starts_with($user->profile_picture, '/storage/profile-photos/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $user->profile_picture));
        }

        $path = $file->store('profile-photos', 'public');

        $user->update(['profile_picture' => '/storage/' . $path]);
    }

    public function setPin(Player $player, string $pin): void
    {
        $player->update(['pin' => $pin]);
    }

    public function deleteAccount(User $user): void
    {
        $user->delete();
    }

    public function playerBelongsToClass(Player $player, int $classId): bool
    {
        return $player->classParticipants()->pluck('school_class_id')->contains($classId);
    }

    public function getPlayerClasses(?Player $player): array
    {
        if (!$player) {
            return [];
        }

        return $player->classParticipants()
            ->with('schoolClass')
            ->get()
            ->map(fn (ClassParticipant $cp) => ['id' => $cp->school_class_id, 'name' => $cp->schoolClass->name])
            ->values()
            ->all();
    }

    private function getParticipantWithRank(User $user, ?int $classId): ?array
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
}
