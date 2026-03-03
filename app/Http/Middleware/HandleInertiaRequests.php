<?php

namespace App\Http\Middleware;

use App\Models\GameMatch;
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
                'user' => $request->user(),
            ],
            'pendingChallengesCount' => fn () => $request->user()
                ? GameMatch::where('challenged_id', $request->user()->id)
                    ->where('status', 'pending')
                    ->count()
                : 0,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
        ];
    }
}
