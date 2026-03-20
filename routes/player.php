<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\player\AccountController;
use App\Http\Controllers\player\Auth\AuthenticatedSessionController;
use App\Http\Controllers\player\Auth\RegisteredPlayerController;
use App\Http\Controllers\player\ClassementController;
use App\Http\Controllers\player\DashboardController;
use App\Http\Controllers\player\PinController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth:player')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('matchs', fn () => Inertia::render('Player/Matchs'))->name('matchs');
    Route::get('classements', [ClassementController::class, 'index'])->name('classements');

    Route::prefix('joueur')->name('player.')->group(function () {
        Route::prefix('profil')->name('account.')->group(function () {
            Route::get('/', [AccountController::class, 'index'])->name('index');
            Route::get('download', [AccountController::class, 'download'])->name('download');
            Route::delete('/', [AccountController::class, 'destroy'])->name('destroy');
            Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
            Route::get('infos', fn () => Inertia::render('Player/InfosPersonnelles'))->name('infos');
            Route::get('confidentialite', [AccountController::class, 'confidentialite'])->name('confidentialite');
        });

        Route::prefix('pin')->name('pin.')->group(function () {
            Route::post('/', [PinController::class, 'store'])->name('store');
        });
    });
});

Route::prefix('player')->name('player.')->group(function () {
    Route::middleware('guest:player')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.submit');
        Route::post('register', [RegisteredPlayerController::class, 'store'])->name('register');
    });

    Route::prefix('auth')->group(function () {
        Route::prefix('google')->name('google.')->group(function () {
            Route::get('redirect', [GoogleAuthController::class, 'redirect'])->name('redirect');
            Route::get('callback', [GoogleAuthController::class, 'callback'])->name('callback');
        });

        Route::get('mot-de-passe-oublie', fn () => Inertia::render('Player/Auth/ForgotPassword'))
            ->name('password.request');
    });

    Route::get('conditions-utilisation', fn () => Inertia::render('Terms'))->name('terms');
    Route::get('politique-de-confidentialite', fn () => Inertia::render('Privacy'))->name('privacy');
});
