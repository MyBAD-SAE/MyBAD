<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ClassParticipant;
use App\Models\Rule;
use App\Services\Ranking\RankingService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminRankingController extends Controller
{
    public function __construct(
        private readonly RankingService $rankingService,
    ) {}


    /*
     *  L'index va donner toutes les props initiales
     */
    /**
     * @OA\Get(
     *     path="/admin/classement",
     *     tags={"Admin - Classement"},
     *     summary="Page classement ELO du cours",
     *     operationId="admin.ranking.index",
     *     security={{"session":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Props Inertia de la page classement",
     *         @OA\JsonContent(
     *             @OA\Property(property="classes", type="array", @OA\Items(@OA\Property(property="id",type="integer"),@OA\Property(property="name",type="string"))),
     *             @OA\Property(property="selectedClassId", type="integer", nullable=true),
     *             @OA\Property(property="playerCount", type="integer"),
     *             @OA\Property(property="rankingPlayers", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="enableRankingGroups", type="boolean"),
     *             @OA\Property(property="enableEloHandicap", type="boolean"),
     *             @OA\Property(property="groupSize", type="integer")
     *         )
     *     )
     * )
     */
    public function index(): Response
    {
        // Récuperation de l'admin
        $user = auth('admin')->user();
        $adminUser = $user->adminUser;

        // Récuperation de tous ses cours
        $classes = $adminUser->classParticipants()
            ->with('schoolClass')
            ->get()
            ->map(fn ($cp) => [
                'id'   => $cp->schoolClass->id,
                'name' => $cp->schoolClass->name,
            ])
            ->values()
            ->all();

        // récuperation de la variable de session pour le cours séléctionné
        $selectedClassId = session('admin_selected_class_id');

        // sinon on prend le premier
        if (!$selectedClassId || !collect($classes)->contains('id', $selectedClassId)) {
            $selectedClassId = $classes[0]['id'] ?? null;
        }

        $playerCount = 0;
        $rankingPlayers = [];
        $enableRankingGroups = false;
        $enableEloHandicap = false;
        $groupSize = 8;

        if ($selectedClassId) {
            // compte le nombre de participants dans le cours
            $playerCount = ClassParticipant::forClass($selectedClassId)
                ->forPlayerType()
                ->count();


            // Recupère le classement complet du cours
            $rankingPlayers = $this->rankingService->getRankingForClassId($selectedClassId);

            // Récuperation de l'enregistrement des defis pour ce cours
            $rule = Rule::where('school_class_id', $selectedClassId)->first();

            //
            if ($rule) {
                $enableRankingGroups = $rule->enable_ranking_groups;
                $enableEloHandicap = $rule->enable_elo_handicap;
                $groupSize = $rule->group_size ?? 8;
            }
        }

        // retourne les props à la page Ranking.vue
        return Inertia::render('Admin/Ranking', [
            'classes'              => $classes,
            'selectedClassId'      => $selectedClassId,
            'playerCount'          => $playerCount,
            'rankingPlayers'       => $rankingPlayers,
            'enableRankingGroups'  => $enableRankingGroups,
            'enableEloHandicap'    => $enableEloHandicap,
            'groupSize'            => $groupSize,
        ]);
    }

    /*
     * data() retourne une réponse JSON avec uniquement les
     * champs qui doivent être rafraîchis pour obtenir le classement dynamique
     */
    /**
     * @OA\Get(
     *     path="/admin/classement/data",
     *     tags={"Admin - Classement"},
     *     summary="Données JSON fraîches du classement (polling temps réel)",
     *     operationId="admin.ranking.data",
     *     security={{"session":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Données du classement en JSON",
     *         @OA\JsonContent(
     *             @OA\Property(property="playerCount", type="integer"),
     *             @OA\Property(property="rankingPlayers", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="enableRankingGroups", type="boolean"),
     *             @OA\Property(property="enableEloHandicap", type="boolean"),
     *             @OA\Property(property="groupSize", type="integer")
     *         )
     *     )
     * )
     */
    public function data(): JsonResponse
    {
        $user = auth('admin')->user();
        $adminUser = $user->adminUser;

        $selectedClassId = session('admin_selected_class_id');

        if (!$selectedClassId) {
            $firstClass = $adminUser->classParticipants()
                ->with('schoolClass')
                ->first();
            $selectedClassId = $firstClass?->schoolClass?->id;
        }

        $playerCount = 0;
        $rankingPlayers = [];
        $enableRankingGroups = false;
        $enableEloHandicap = false;
        $groupSize = 8;

        if ($selectedClassId) {
            $playerCount = ClassParticipant::forClass($selectedClassId)
                ->forPlayerType()
                ->count();

            $rankingPlayers = $this->rankingService->getRankingForClassId($selectedClassId);

            $rule = Rule::where('school_class_id', $selectedClassId)->first();

            if ($rule) {
                $enableRankingGroups = $rule->enable_ranking_groups;
                $enableEloHandicap = $rule->enable_elo_handicap;
                $groupSize = $rule->group_size ?? 8;
            }
        }

        return response()->json([
            'playerCount'         => $playerCount,
            'rankingPlayers'      => $rankingPlayers,
            'enableRankingGroups' => $enableRankingGroups,
            'enableEloHandicap'   => $enableEloHandicap,
            'groupSize'           => $groupSize,
        ]);
    }
}
