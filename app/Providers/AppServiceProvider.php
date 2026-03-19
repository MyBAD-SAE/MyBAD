<?php

namespace App\Providers;

use App\Models\GameMatch;
use App\Policies\MatchPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Gate::policy(GameMatch::class, MatchPolicy::class);
        Route::model('match', GameMatch::class);

        Inertia::share([
            'auth' => function () {
                $user = auth()->user();

                return [
                    'user' => $user,
                ];
            },
        ]);
    }
}
