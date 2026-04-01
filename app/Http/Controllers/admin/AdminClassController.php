<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
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
