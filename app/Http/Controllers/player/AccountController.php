<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Player\DeleteAccountRequest;
use App\Http\Requests\Player\SelectClassRequest;
use App\Http\Requests\Player\UpdatePhotoRequest;
use App\Http\Requests\Player\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Player\PlayerExportService;
use App\Services\Player\PlayerProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function __construct(private PlayerProfileService $profileService) {}

    public function index(): Response
    {
        /** @var User $user */
        $user = auth('player')->user();

        return Inertia::render('Player/Profile', $this->profileService->getProfileData($user));
    }

    public function infos(): Response
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        return Inertia::render('Player/PersonalInfo', [
            'user' => UserResource::make($user)->resolve(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        $this->profileService->updateProfile($user, $request->validated());

        return redirect()->route('player.account.infos');
    }

    public function privacy(PlayerExportService $export): Response
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        return Inertia::render('Player/Privacy', $export->summary($user));
    }

    public function download(PlayerExportService $export): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        return response()->json($export->build($user), 200, [
            'Content-Disposition' => 'attachment; filename="' . $export->filename() . '"',
        ]);
    }

    public function selectClass(SelectClassRequest $request): RedirectResponse
    {
        $player = auth('player')->user()->player;

        if ($this->profileService->playerBelongsToClass($player, $request->class_id)) {
            session(['selected_class_id' => (int) $request->class_id]);
        }

        return redirect()->back();
    }

    public function destroy(DeleteAccountRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        $this->profileService->deleteAccount($user);

        Auth::guard('player')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('player.login');
    }

    public function updatePhoto(UpdatePhotoRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        $this->profileService->updatePhoto($user, $request->file('photo'));

        return back();
    }
}
