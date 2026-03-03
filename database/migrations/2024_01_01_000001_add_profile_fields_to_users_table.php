<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('name');
            $table->integer('elo_rating')->default(1000)->after('avatar');
            $table->unsignedInteger('matches_played')->default(0)->after('elo_rating');
            $table->unsignedInteger('matches_won')->default(0)->after('matches_played');
            $table->unsignedInteger('matches_lost')->default(0)->after('matches_won');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'elo_rating', 'matches_played', 'matches_won', 'matches_lost']);
        });
    }
};
