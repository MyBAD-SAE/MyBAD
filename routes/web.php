<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Page d'accueil (protégée)
Route::middleware('auth:player')->group(function () {
    Route::get('/', fn () => Inertia::render('players/Dashboard'))->name('home');
});
