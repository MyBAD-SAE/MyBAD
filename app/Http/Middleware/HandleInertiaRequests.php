<?php

namespace App\Http\Middleware;

use App\Models\ClassSession;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user('player')?->load('player') ?? $request->user('admin'),
                'hasPin' => $request->user('player') ? (bool) $request->user('player')->player?->pin : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
            'hasActiveSession' => function () use ($request) {
                $player = $request->user('player')?->player;
                $classId = $player?->selectedParticipation()?->school_class_id;
                if (!$classId) return false;
                return ClassSession::forClass($classId)->active()->exists();
            },
        ];
    }
}
