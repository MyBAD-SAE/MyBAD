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

    /**
     * @OA\Get(
     *     path="/joueur/profil",
     *     tags={"Joueur - Compte"},
     *     summary="Page de profil joueur",
     *     operationId="player.account.index",
     *     security={{"session":{}}},
     *     @OA\Response(response=200, description="Props Inertia de la page profil")
     * )
     */
    public function index(): Response
    {
        /** @var User $user */
        $user = auth('player')->user();

        return Inertia::render('Player/Profile', $this->profileService->getProfileData($user));
    }

    /**
     * @OA\Get(
     *     path="/joueur/profil/infos",
     *     tags={"Joueur - Compte"},
     *     summary="Informations personnelles du joueur",
     *     operationId="player.account.infos",
     *     security={{"session":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Props Inertia de la page informations personnelles",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="first_name", type="string"),
     *                 @OA\Property(property="last_name", type="string"),
     *                 @OA\Property(property="email", type="string", format="email")
     *             )
     *         )
     *     )
     * )
     */
    public function infos(): Response
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        return Inertia::render('Player/PersonalInfo', [
            'user' => UserResource::make($user)->resolve(),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/joueur/profil/infos",
     *     tags={"Joueur - Compte"},
     *     summary="Mettre à jour les informations personnelles",
     *     operationId="player.account.update",
     *     security={{"session":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string", maxLength=255),
     *             @OA\Property(property="last_name", type="string", maxLength=255),
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(response=302, description="Redirection vers /joueur/profil/infos"),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        $this->profileService->updateProfile($user, $request->validated());

        return redirect()->route('player.account.infos');
    }

    /**
     * @OA\Get(
     *     path="/joueur/profil/confidentialite",
     *     tags={"Joueur - Compte"},
     *     summary="Page confidentialité avec résumé des données personnelles",
     *     operationId="player.account.privacy",
     *     security={{"session":{}}},
     *     @OA\Response(response=200, description="Résumé des données personnelles")
     * )
     */
    public function privacy(PlayerExportService $export): Response
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        return Inertia::render('Player/Privacy', $export->summary($user));
    }

    /**
     * @OA\Get(
     *     path="/joueur/profil/download",
     *     tags={"Joueur - Compte"},
     *     summary="Télécharger ses données personnelles (JSON)",
     *     operationId="player.account.download",
     *     security={{"session":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Export JSON des données du joueur (Content-Disposition: attachment)",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function download(PlayerExportService $export): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        return response()->json($export->build($user), 200, [
            'Content-Disposition' => 'attachment; filename="' . $export->filename() . '"',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/joueur/classe",
     *     tags={"Joueur - Compte"},
     *     summary="Sélectionner la classe active du joueur",
     *     operationId="player.class.select",
     *     security={{"session":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"class_id"},
     *             @OA\Property(property="class_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=302, description="Retour vers la page précédente")
     * )
     */
    public function selectClass(SelectClassRequest $request): RedirectResponse
    {
        $player = auth('player')->user()->player;

        if ($this->profileService->playerBelongsToClass($player, $request->class_id)) {
            session(['selected_class_id' => (int) $request->class_id]);
        }

        return redirect()->back();
    }

    /**
     * @OA\Delete(
     *     path="/joueur/profil",
     *     tags={"Joueur - Compte"},
     *     summary="Supprimer son compte joueur",
     *     operationId="player.account.destroy",
     *     security={{"session":{}}},
     *     @OA\Response(response=302, description="Redirection vers /player/login")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/joueur/profil/photo",
     *     tags={"Joueur - Compte"},
     *     summary="Mettre à jour la photo de profil",
     *     operationId="player.account.photo",
     *     security={{"session":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"photo"},
     *                 @OA\Property(property="photo", type="string", format="binary", description="Fichier image (jpg/png/gif, max 2Mo)")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=302, description="Retour vers la page précédente"),
     *     @OA\Response(response=422, description="Fichier invalide")
     * )
     */
    public function updatePhoto(UpdatePhotoRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::guard('player')->user();

        $this->profileService->updatePhoto($user, $request->file('photo'));

        return back();
    }
}
