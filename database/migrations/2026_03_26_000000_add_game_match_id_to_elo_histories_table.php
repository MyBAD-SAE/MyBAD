<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('elo_histories')->truncate();

        Schema::table('elo_histories', function (Blueprint $table) {
            $table->foreignId('game_match_id')
                ->after('participant_id')
                ->constrained('game_matches')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('elo_histories', function (Blueprint $table) {
            $table->dropForeign(['game_match_id']);
            $table->dropColumn('game_match_id');
        });
    }
};
