<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Dashboard joueur
Route::middleware('auth:player')->group(function () {
    Route::get('/', fn () => Inertia::render('Player/Dashboard'))->name('home');
});

// Dashboard admin
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin', fn () => Inertia::render('Admin/Dashboard'))->name('admin.dashboard');
});
