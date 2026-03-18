<?php

use App\Http\Controllers\player\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('auth/player')->name('player.')->group(function () {
    Route::middleware('guest:player')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AuthenticatedSessionController::class, 'store'])
            ->name('login.submit');
    });

    Route::middleware('auth:player')->group(function () {
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
    });
});


Route::get('auth/mot-de-passe-oublie', fn () => Inertia::render('ForgotPassword'))
    ->name('password.request');

Route::get('/conditions-utilisation', fn () => Inertia::render('Terms'))->name('terms');
Route::get('/politique-de-confidentialite', fn () => Inertia::render('Privacy'))->name('privacy');
