<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Page d'accueil (protégée)
Route::middleware('auth:player')->group(function () {
    Route::get('/', fn () => Inertia::render('Index'))->name('home');
});

// TODO: à mettre dans un groupe de routes protégées par auth:player
Route::get('/declarer-un-match', fn () => Inertia::render('DeclarationMatch'))->name('match.declare');