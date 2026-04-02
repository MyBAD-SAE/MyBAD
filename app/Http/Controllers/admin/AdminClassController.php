<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\AlgorithmParameter;
use App\Models\ClassParticipant;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminClassController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Admin/Class/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_year' => 'required|string|max:20',
            'name'        => 'required|string|max:100',
            'address'     => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $adminUser = auth('admin')->user()->adminUser;

        $class = SchoolClass::create($validated);

        $defaultParameters = [
            ['min_diff' => -20, 'max_diff' => -12, 'winner_points' => -0.7],
            ['min_diff' => -11, 'max_diff' =>  -7, 'winner_points' => -0.2],
            ['min_diff' =>  -6, 'max_diff' =>  -1, 'winner_points' =>  0.0],
            ['min_diff' =>   0, 'max_diff' =>   6, 'winner_points' =>  0.5],
            ['min_diff' =>   7, 'max_diff' =>  11, 'winner_points' =>  1.0],
            ['min_diff' =>  12, 'max_diff' =>  20, 'winner_points' =>  1.6],
        ];

        foreach ($defaultParameters as $param) {
            AlgorithmParameter::create([...$param, 'school_class_id' => $class->id]);
        }

        ClassParticipant::create([
            'participantable_type' => AdminUser::class,
            'participantable_id'   => $adminUser->id,
            'school_class_id'      => $class->id,
            'elo_rating'           => null,
        ]);

        session(['admin_selected_class_id' => $class->id]);

        return redirect()->route('admin.dashboard')->with('success', 'Cours créé avec succès.');
    }
}
