<?php

use App\Http\Controllers\player\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Dashboard joueur
Route::middleware('auth:player')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/matchs', fn () => Inertia::render('Player/Matchs'))->name('matchs');
    Route::get('/classements', fn () => Inertia::render('Player/Classements'))->name('classements');
    Route::get('/profil', fn () => Inertia::render('Player/Profil'))->name('profil');
});

// Dashboard admin
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin', fn () => Inertia::render('Admin/Dashboard'))->name('admin.dashboard');
});
