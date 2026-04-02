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
        $adminUser = $request->user('admin');

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user('player')?->load('player') ?? $adminUser,
                'hasPin' => $request->user('player') ? (bool) $request->user('player')->player?->pin : null,
            ],
            'adminUser' => $adminUser ? [
                'firstName' => $adminUser->first_name,
                'fullName'  => $adminUser->first_name . ' ' . mb_substr($adminUser->last_name, 0, 1) . '.',
                'avatar'    => $adminUser->profile_picture,
            ] : null,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
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
