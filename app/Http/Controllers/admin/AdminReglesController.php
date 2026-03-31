<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AlgorithmParameter;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminReglesController extends Controller
{
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
        }

        return Inertia::render('Admin/Regles', [
            'classes' => $classes,
            'selectedClassId' => $selectedClassId,
            'parameters' => $parameters,
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
            return redirect()->route('admin.regles');
        }

        foreach ($request->input('parameters') as $param) {
            AlgorithmParameter::where('id', $param['id'])
                ->where('school_class_id', $selectedClassId)
                ->update(['winner_points' => $param['winner_points']]);
        }

        return redirect()->route('admin.regles');
    }
}
