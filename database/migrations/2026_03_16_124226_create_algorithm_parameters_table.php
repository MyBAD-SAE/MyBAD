<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('algorithm_parameters', function (Blueprint $table) {
            $table->id();
            $table->integer('min_diff');
            $table->integer('max_diff');
            $table->float('winner_points');
            $table->foreignId('class_id')->constrained('classes')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('algorithm_parameters');
    }
};
