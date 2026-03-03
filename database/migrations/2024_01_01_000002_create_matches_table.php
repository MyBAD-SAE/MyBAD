<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenger_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('challenged_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'accepted', 'completed', 'declined', 'cancelled'])->default('pending');

            // Scores per set (best-of-3)
            $table->unsignedTinyInteger('challenger_score_set1')->nullable();
            $table->unsignedTinyInteger('challenged_score_set1')->nullable();
            $table->unsignedTinyInteger('challenger_score_set2')->nullable();
            $table->unsignedTinyInteger('challenged_score_set2')->nullable();
            $table->unsignedTinyInteger('challenger_score_set3')->nullable();
            $table->unsignedTinyInteger('challenged_score_set3')->nullable();

            $table->timestamp('played_at')->nullable();
            $table->integer('elo_change')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
