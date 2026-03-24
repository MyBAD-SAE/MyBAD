<?php

use App\Http\Controllers\player\AccountController;
use App\Http\Controllers\player\Auth\AuthenticatedSessionController;
use App\Http\Controllers\player\Auth\GoogleAuthController;
use App\Http\Controllers\player\Auth\PasswordResetController;
use App\Http\Controllers\player\Auth\RegisteredPlayerController;
use App\Http\Controllers\player\RankingController;
use App\Http\Controllers\player\DashboardController;
use App\Http\Controllers\player\MatchDeclarationController;
use App\Http\Controllers\player\PinController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth:player')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('matchs', fn() => Inertia::render('Player/Matchs'))->name('matchs');
    Route::get('classements', [RankingController::class, 'index'])->name('classements');
    Route::get('historique-matchs', fn () => Inertia::render('Player/HistoriqueMatchs'))->name('historique.matchs');

    Route::prefix('declarer-un-match')
        ->name('match.')
        ->controller(MatchDeclarationController::class)
        ->group(function () {
            Route::get('/', 'create')->name('declare');
            Route::get('/adversaires', 'opponents')->name('opponents');
            Route::post('/verify-pin', 'verifyPin')->name('verify-pin');
            Route::post('/', 'store')->name('store');
        });

    Route::prefix('joueur')->name('player.')->group(function () {
        Route::prefix('profil')->name('account.')->group(function () {
            Route::get('/', [AccountController::class, 'index'])->name('index');
            Route::get('download', [AccountController::class, 'download'])->name('download');
            Route::delete('/', [AccountController::class, 'destroy'])->name('destroy');
            Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
            Route::get('infos', [AccountController::class, 'infos'])->name('infos');
            Route::put('infos', [AccountController::class, 'update'])->name('infos.update');
            Route::post('photo', [AccountController::class, 'updatePhoto'])->name('photo.update');
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

        Route::get('mot-de-passe-oublie', fn() => Inertia::render('Player/Auth/ForgotPassword'))
            ->name('password.request');
        Route::post('mot-de-passe-oublie', [PasswordResetController::class, 'sendResetLink'])
            ->name('password.email');
        Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
            ->name('password.reset');
        Route::post('reset-password', [PasswordResetController::class, 'reset'])
            ->name('password.update');
    });

    Route::get('conditions-utilisation', fn() => Inertia::render('Terms'))->name('terms');
    Route::get('politique-de-confidentialite', fn() => Inertia::render('Privacy'))->name('privacy');
});
