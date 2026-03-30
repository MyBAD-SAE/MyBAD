<?php

use App\Http\Controllers\admin\AdminAccountController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminMatchsController;
use App\Http\Controllers\admin\AdminPlayersController;
use App\Http\Controllers\admin\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\admin\Auth\RegisteredAdminController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AdminAuthenticatedSessionController::class, 'store'])
            ->name('login.submit');

        // Route::post('register', [RegisteredAdminController::class, 'store'])
        //     ->name('register');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('class/select', [AdminDashboardController::class, 'selectClass'])->name('class.select');

        Route::get('account', [AdminAccountController::class, 'index'])->name('account');
        Route::put('account/profile', [AdminAccountController::class, 'updateProfile'])->name('account.profile');
        Route::put('account/password', [AdminAccountController::class, 'updatePassword'])->name('account.password');

        Route::get('joueurs', [AdminPlayersController::class, 'index'])->name('joueurs');
        Route::post('joueurs', [AdminPlayersController::class, 'store'])->name('joueurs.store');
        Route::put('joueurs/{participant}', [AdminPlayersController::class, 'update'])->name('joueurs.update');
        Route::delete('joueurs/{participant}', [AdminPlayersController::class, 'destroy'])->name('joueurs.destroy');

        Route::get('matchs', [AdminMatchsController::class, 'index'])->name('matchs');
        Route::put('matchs/{gameMatch}', [AdminMatchsController::class, 'update'])->name('matchs.update');
        Route::delete('matchs/{gameMatch}', [AdminMatchsController::class, 'destroy'])->name('matchs.destroy');
        Route::get('regles', fn () => Inertia::render('Admin/Regles'))->name('regles');
    });
});
