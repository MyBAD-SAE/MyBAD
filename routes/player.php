<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\player\Auth\AuthenticatedSessionController;
use App\Http\Controllers\player\Auth\RegisteredPlayerController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('player')->name('player.')->group(function () {
    Route::middleware('guest:player')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AuthenticatedSessionController::class, 'store'])
            ->name('login.submit');

        Route::post('register', [RegisteredPlayerController::class, 'store'])
            ->name('register');
    });

    Route::middleware('auth:player')->group(function () {
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
    });
});


Route::get('auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

Route::get('auth/mot-de-passe-oublie', fn () => Inertia::render('Player/Auth/ForgotPassword'))
    ->name('password.request');

Route::get('/conditions-utilisation', fn () => Inertia::render('Terms'))->name('terms');
Route::get('/politique-de-confidentialite', fn () => Inertia::render('Privacy'))->name('privacy');
