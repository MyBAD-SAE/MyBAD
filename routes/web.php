<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth');
})->name('auth');

Route::get('/mot-de-passe-oublie', function () {
    return Inertia::render('ForgotPassword');
})->name('password.request');
