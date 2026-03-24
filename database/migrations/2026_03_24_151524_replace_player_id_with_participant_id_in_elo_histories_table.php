<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('elo_histories')->truncate();

        Schema::table('elo_histories', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->dropColumn('player_id');
            $table->foreignId('participant_id')->after('id')->constrained('class_participants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        DB::table('elo_histories')->truncate();

        Schema::table('elo_histories', function (Blueprint $table) {
            $table->dropForeign(['participant_id']);
            $table->dropColumn('participant_id');
            $table->string('player_id')->after('id');
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
        });
    }
};
