<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Arrondir les valeurs existantes
        DB::table('class_participants')
            ->whereNotNull('elo_rating')
            ->update(['elo_rating' => DB::raw('ROUND(elo_rating)')]);

        DB::table('elo_histories')->update([
            'elo_before' => DB::raw('ROUND(elo_before)'),
            'elo_after'  => DB::raw('ROUND(elo_after)'),
        ]);

        // Convertir les colonnes en integer
        Schema::table('class_participants', function (Blueprint $table) {
            $table->integer('elo_rating')->nullable()->default(1000)->change();
        });

        Schema::table('elo_histories', function (Blueprint $table) {
            $table->integer('elo_before')->change();
            $table->integer('elo_after')->change();
        });
    }

    public function down(): void
    {
        Schema::table('class_participants', function (Blueprint $table) {
            $table->decimal('elo_rating', 7, 1)->nullable()->default(1000)->change();
        });

        Schema::table('elo_histories', function (Blueprint $table) {
            $table->decimal('elo_before', 7, 1)->change();
            $table->decimal('elo_after', 7, 1)->change();
        });
    }
};