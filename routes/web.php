<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Dashboard joueur
Route::middleware('auth:player')->group(function () {
    Route::get('/', fn () => Inertia::render('Player/Dashboard'))->name('home');
    Route::get('/matchs', fn () => Inertia::render('Player/Matchs'))->name('matchs');
    Route::get('/classements', fn () => Inertia::render('Player/Classements'))->name('classements');
    Route::get('/profil', fn () => Inertia::render('Player/Profil'))->name('profil');
    Route::get('/profil/infos', fn () => Inertia::render('Player/InfosPersonnelles'))->name('profil.infos');
});

// Dashboard admin
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin', fn () => Inertia::render('Admin/Dashboard'))->name('admin.dashboard');
});
