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
    Route::get('/profil/confidentialite', function () {
        $user = auth()->guard('player')->user();
        $player = $user->player;

        $profileData = json_encode([
            'prenom' => $user->first_name,
            'nom' => $user->last_name,
            'email' => $user->email,
            'photo' => $user->profile_picture,
            'compte_cree_le' => $user->created_at,
        ]);

        $eloData = json_encode($player->eloHistories()->get(['elo_before', 'elo_after', 'created_at']));

        $matchData = json_encode($player->gameMatches()->get());

        return Inertia::render('Player/Confidentialite', [
            'matchCount' => $player->gameMatches()->count(),
            'eloHistoryCount' => $player->eloHistories()->count(),
            'profileSize' => strlen($profileData),
            'eloSize' => strlen($eloData),
            'matchSize' => strlen($matchData),
        ]);
    })->name('profil.confidentialite');
});

// Dashboard admin
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin', fn () => Inertia::render('Admin/Dashboard'))->name('admin.dashboard');
});
