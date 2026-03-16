<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('elo_histories', function (Blueprint $table) {
            $table->id();
            $table->float('elo_before');
            $table->float('elo_after');
            $table->uuid('player_id');
            $table->foreign('player_id')->references('id')->on('players')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elo_histories');
    }
};
