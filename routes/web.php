<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth');
})->name('auth');

Route::get('/mot-de-passe-oublie', function () {
    return Inertia::render('ForgotPassword');
})->name('password.request');

Route::get('/conditions-utilisation', function () {
    return Inertia::render('Terms');
})->name('terms');

Route::get('/politique-de-confidentialite', function () {
    return Inertia::render('Privacy');
})->name('privacy');

Route::get('/admin', function () {
    return Inertia::render('AdminAuth');
})->name('admin.auth');
