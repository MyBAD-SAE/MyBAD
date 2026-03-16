<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elo_histories', function (Blueprint $table) {
            $table->id();
            $table->float('elo_before');
            $table->float('elo_after');
            $table->uuidMorphs('player');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elo_histories');
    }
};
