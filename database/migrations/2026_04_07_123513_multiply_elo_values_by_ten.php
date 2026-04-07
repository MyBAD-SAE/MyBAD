<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // Multiplier par 10 toutes les valeurs ELO existantes
        DB::table('class_participants')
            ->whereNotNull('elo_rating')
            ->update(['elo_rating' => DB::raw('elo_rating * 10')]);

        DB::table('elo_histories')->update([
            'elo_before' => DB::raw('elo_before * 10'),
            'elo_after'  => DB::raw('elo_after * 10'),
        ]);

        DB::table('algorithm_parameters')->update([
            'winner_points' => DB::raw('winner_points * 10'),
        ]);

        // Changer le default de elo_rating de 100 à 1000
        Schema::table('class_participants', function (Blueprint $table) {
            $table->decimal('elo_rating', 7, 1)->nullable()->default(1000)->change();
        });

        // Adapter la précision des colonnes elo_histories
        Schema::table('elo_histories', function (Blueprint $table) {
            $table->decimal('elo_before', 7, 1)->change();
            $table->decimal('elo_after', 7, 1)->change();
        });
    }

    public function down(): void
    {
        Schema::table('class_participants', function (Blueprint $table) {
            $table->decimal('elo_rating', 6, 2)->nullable()->default(100)->change();
        });

        Schema::table('elo_histories', function (Blueprint $table) {
            $table->decimal('elo_before', 6, 2)->change();
            $table->decimal('elo_after', 6, 2)->change();
        });

        DB::table('class_participants')
            ->whereNotNull('elo_rating')
            ->update(['elo_rating' => DB::raw('elo_rating / 10')]);

        DB::table('elo_histories')->update([
            'elo_before' => DB::raw('elo_before / 10'),
            'elo_after'  => DB::raw('elo_after / 10'),
        ]);

        DB::table('algorithm_parameters')->update([
            'winner_points' => DB::raw('winner_points / 10'),
        ]);
    }
};
