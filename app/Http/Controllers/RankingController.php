<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;

class RankingController extends Controller
{
    public function index()
    {
        $players = User::orderByDesc('elo_rating')
            ->orderByDesc('matches_won')
            ->get(['id', 'name', 'avatar', 'elo_rating', 'matches_played', 'matches_won', 'matches_lost']);

        return Inertia::render('Rankings/Index', [
            'players' => $players,
        ]);
    }
}
