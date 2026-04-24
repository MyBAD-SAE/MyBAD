<?php

namespace App\Http\Controllers\player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Player\StorePinRequest;
use App\Services\Player\PlayerProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PinController extends Controller
{
    public function __construct(private readonly PlayerProfileService $profileService) {}

    /**
     * @OA\Post(
     *     path="/joueur/pin",
     *     tags={"Joueur - Compte"},
     *     summary="Définir ou modifier le PIN du joueur",
     *     operationId="player.pin.store",
     *     security={{"session":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"pin"},
     *             @OA\Property(property="pin", type="string", minLength=4, maxLength=4, pattern="^\\d{4}$", example="1234", description="Code PIN à 4 chiffres")
     *         )
     *     ),
     *     @OA\Response(response=302, description="Retour vers la page précédente"),
     *     @OA\Response(response=422, description="Le PIN doit être 4 chiffres")
     * )
     */
    public function store(StorePinRequest $request): RedirectResponse
    {
        $this->profileService->setPin(Auth::guard('player')->user()->player, $request->pin);

        return back();
    }
}
