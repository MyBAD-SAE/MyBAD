<?php

use App\Http\Controllers\admin\AdminAccountController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminMatchesController;
use App\Http\Controllers\admin\AdminPlayersController;
use App\Http\Controllers\admin\AdminRankingController;
use App\Http\Controllers\admin\AdminRulesController;
use App\Http\Controllers\admin\AdminSessionController;
use App\Http\Controllers\admin\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\admin\Auth\AdminPasswordResetController;
use App\Http\Controllers\admin\Auth\RegisteredAdminController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AdminAuthenticatedSessionController::class, 'store'])
            ->name('login.submit');

         Route::post('register', [RegisteredAdminController::class, 'store'])
             ->name('register');

        Route::get('forgot-password', [AdminPasswordResetController::class, 'showForgotForm'])
            ->name('password.request');
        Route::post('forgot-password', [AdminPasswordResetController::class, 'sendResetLink'])
            ->name('password.email');
        Route::get('reset-password/{token}', [AdminPasswordResetController::class, 'showResetForm'])
            ->name('password.reset');
        Route::post('reset-password', [AdminPasswordResetController::class, 'reset'])
            ->name('password.update');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('class/select', [AdminDashboardController::class, 'selectClass'])->name('class.select');

        Route::get('account', [AdminAccountController::class, 'index'])->name('account');
        Route::put('account/profile', [AdminAccountController::class, 'updateProfile'])->name('account.profile');
        Route::put('account/password', [AdminAccountController::class, 'updatePassword'])->name('account.password');

        Route::get('joueurs', [AdminPlayersController::class, 'index'])->name('players');
        Route::post('joueurs', [AdminPlayersController::class, 'store'])->name('players.store');
        Route::put('joueurs/{participant}', [AdminPlayersController::class, 'update'])->name('players.update');
        Route::delete('joueurs/{participant}', [AdminPlayersController::class, 'destroy'])->name('players.destroy');

        Route::get('matchs', [AdminMatchesController::class, 'index'])->name('matches');
        Route::put('matchs/{gameMatch}', [AdminMatchesController::class, 'update'])->name('matches.update');
        Route::delete('matchs/{gameMatch}', [AdminMatchesController::class, 'destroy'])->name('matches.destroy');
        Route::get('classement', [AdminRankingController::class, 'index'])->name('ranking');
        Route::get('classement/data', [AdminRankingController::class, 'data'])->name('ranking.data');

        Route::get('regles', [AdminRulesController::class, 'index'])->name('rules');
        Route::put('regles', [AdminRulesController::class, 'update'])->name('rules.update');
        Route::put('regles/defis', [AdminRulesController::class, 'updateRule'])->name('rules.updateRule');

        Route::get('session', [AdminSessionController::class, 'show'])->name('session');
        Route::post('session', [AdminSessionController::class, 'store'])->name('session.store');
        Route::post('session/close', [AdminSessionController::class, 'close'])->name('session.close');
    });
});