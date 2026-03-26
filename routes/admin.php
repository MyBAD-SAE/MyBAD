<?php

use App\Http\Controllers\admin\AdminDashboardController;
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

        Route::get('matchs', fn () => Inertia::render('Admin/Matchs'))->name('matchs');
        Route::get('regles', fn () => Inertia::render('Admin/Regles'))->name('regles');
    });
});
