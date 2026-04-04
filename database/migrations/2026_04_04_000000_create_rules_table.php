<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('enable_ranking_groups')->default(false);
            $table->boolean('enable_elo_handicap')->default(false);
            $table->integer('group_size')->nullable();
            $table->foreignId('school_class_id')
                ->constrained('school_classes')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rules');
    }
};