<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ClassParticipant;
use App\Models\ClassSession;
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

    /**
     * @OA\Get(
     *     path="/admin/dashboard",
     *     tags={"Admin - Tableau de bord"},
     *     summary="Tableau de bord administrateur",
     *     operationId="admin.dashboard",
     *     security={{"session":{}}},
     *     @OA\Parameter(name="period", in="query", required=false, description="Période de stats : '30j' ou 'all'", @OA\Schema(type="string", enum={"30j","all"}, default="30j")),
     *     @OA\Response(
     *         response=200,
     *         description="Props Inertia du tableau de bord",
     *         @OA\JsonContent(
     *             @OA\Property(property="classes", type="array", @OA\Items(@OA\Property(property="id",type="integer"), @OA\Property(property="name",type="string"))),
     *             @OA\Property(property="selectedClassId", type="integer", nullable=true),
     *             @OA\Property(property="playerCount", type="integer"),
     *             @OA\Property(property="matchCount", type="integer"),
     *             @OA\Property(property="rankingPlayers", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="period", type="string"),
     *             @OA\Property(property="hasActiveSession", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=302, description="Redirection vers /admin/login si non authentifié")
     * )
     */
    public function index(Request $request): Response
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

        $period = $request->input('period', '30j');

        $playerCount = 0;
        $matchCount = 0;
        $rankingPlayers = [];

        if ($selectedClassId) {
            $playerCount = ClassParticipant::forClass($selectedClassId)
                ->forPlayerType()
                ->count();

            $matchCount = GameMatch::forClass($selectedClassId)->count();

            $since = $period === '30j' ? now()->subDays(30) : null;
            $rankingPlayers = $this->rankingService->getRankingForClassId($selectedClassId, $since);
        }

        $hasActiveSession = $selectedClassId
            ? ClassSession::forClass($selectedClassId)->active()->exists()
            : false;

        return Inertia::render('Admin/Dashboard', [
            'classes'          => $classes,
            'selectedClassId'  => $selectedClassId,
            'playerCount'      => $playerCount,
            'matchCount'       => $matchCount,
            'rankingPlayers'   => $rankingPlayers,
            'period'           => $period,
            'hasActiveSession' => $hasActiveSession,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/admin/class/select",
     *     tags={"Admin - Tableau de bord"},
     *     summary="Sélectionner le cours actif en session",
     *     operationId="admin.class.select",
     *     security={{"session":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"class_id"},
     *             @OA\Property(property="class_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=302, description="Redirection vers la page précédente"),
     *     @OA\Response(response=403, description="Accès interdit à ce cours")
     * )
     */
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
