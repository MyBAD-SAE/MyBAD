<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('auth/admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', fn () => Inertia::render('Admin/Auth'))
            ->name('login');
    });
});
