<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('match_player', function (Blueprint $table) {
            $table->foreignId('game_match_id')->constrained('game_matches')->onDelete('cascade');
            $table->foreignUuid('player_id')->constrained('players')->onDelete('cascade');

            $table->integer('score')->default(0);
            $table->boolean('validated')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_player');
    }
};
