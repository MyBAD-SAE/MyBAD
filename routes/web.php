<?php

use App\Http\Controllers\PublicRankingController;
use Illuminate\Support\Facades\Route;

Route::get('live/{token}', [PublicRankingController::class, 'show'])->name('live.show');
Route::get('live/{token}/data', [PublicRankingController::class, 'data'])->name('live.data');

require __DIR__.'/player.php';
require __DIR__.'/admin.php';
