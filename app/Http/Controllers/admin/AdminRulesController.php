<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRuleRequest;
use App\Models\AlgorithmParameter;
use App\Services\Rule\RuleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class AdminRulesController extends Controller
{
    public function __construct(
        private readonly RuleService $ruleService,
    ) {}

    public function index(): Response
    {
        $user = auth('admin')->user();
        $adminUser = $user->adminUser;

        $classes = $adminUser->classParticipants()
            ->with('schoolClass')
            ->get()
            ->map(fn ($cp) => [
                'id' => $cp->schoolClass->id,
                'name' => $cp->schoolClass->name,
            ])
            ->values()
            ->all();

        $selectedClassId = session('admin_selected_class_id');

        if (! $selectedClassId || ! collect($classes)->contains('id', $selectedClassId)) {
            $selectedClassId = $classes[0]['id'] ?? null;
        }

        $parameters = [];
        $rule = null;

        if ($selectedClassId) {
            $parameters = AlgorithmParameter::where('school_class_id', $selectedClassId)
                ->orderBy('min_diff')
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'minDiff' => $p->min_diff,
                    'maxDiff' => $p->max_diff,
                    'winnerPoints' => $p->winner_points,
                ])
                ->all();

            $ruleModel = $this->ruleService->getRuleForClass($selectedClassId);

            if ($ruleModel) {
                $rule = [
                    'enableRankingGroups' => $ruleModel->enable_ranking_groups,
                    'enableEloHandicap' => $ruleModel->enable_elo_handicap,
                    'groupSize' => $ruleModel->group_size ?? 8,
                    'handicapParameters' => $ruleModel->handicapParameters
                        ->map(fn ($hp) => [
                            'from' => $hp->min_gap,
                            'to' => $hp->max_gap,
                            'points' => $hp->handicap,
                        ])
                        ->all(),
                ];
            }
        }

        return Inertia::render('Admin/Rules', [
            'classes' => $classes,
            'selectedClassId' => $selectedClassId,
            'parameters' => $parameters,
            'rule' => $rule,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'parameters' => 'required|array',
            'parameters.*.id' => 'required|exists:algorithm_parameters,id',
            'parameters.*.winner_points' => 'required|numeric',
        ]);

        $selectedClassId = session('admin_selected_class_id');

        if (! $selectedClassId) {
            $user = auth('admin')->user();
            $firstClass = $user->adminUser->classParticipants()
                ->with('schoolClass')
                ->first();
            $selectedClassId = $firstClass?->schoolClass?->id;
        }

        if (! $selectedClassId) {
            return redirect()->route('admin.rules');
        }

        foreach ($request->input('parameters') as $param) {
            AlgorithmParameter::where('id', $param['id'])
                ->where('school_class_id', $selectedClassId)
                ->update(['winner_points' => $param['winner_points']]);
        }

        return redirect()->route('admin.rules')->with('success', 'Règles enregistrées avec succès.');
    }

    public function updateRule(UpdateRuleRequest $request): RedirectResponse
    {
        $selectedClassId = session('admin_selected_class_id');

        if (! $selectedClassId) {
            $user = auth('admin')->user();
            $firstClass = $user->adminUser->classParticipants()
                ->with('schoolClass')
                ->first();
            $selectedClassId = $firstClass?->schoolClass?->id;
        }

        if (! $selectedClassId) {
            return redirect()->route('admin.rules');
        }

        $this->ruleService->updateRule($selectedClassId, $request->validated());

        return redirect()->route('admin.rules')->with('success', 'Défis enregistrés avec succès.');
    }
}