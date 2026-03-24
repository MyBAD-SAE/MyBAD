<?php

namespace App\Providers;

use App\Models\GameMatch;
use App\Policies\MatchPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\URL;
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
        // FORCE LE HTTPS EN PRODUCTION
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Vite::prefetch(concurrency: 3);

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return url(route('player.password.reset', ['token' => $token, 'email' => $user->email], false));
        });
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
