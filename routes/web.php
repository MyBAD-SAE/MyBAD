<?php

use Illuminate\Support\Facades\Route;

Route::get('/docs/guide', fn() => response()->file(base_path('docs/guide-utilisateur.html')))->name('docs.guide');
Route::get('/docs/api', fn() => response()->file(base_path('docs/redoc.html')))->name('docs.api');

require __DIR__.'/player.php';
require __DIR__.'/admin.php';
