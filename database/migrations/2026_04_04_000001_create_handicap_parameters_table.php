<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('handicap_parameters', function (Blueprint $table) {
            $table->id();
            $table->integer('min_gap');
            $table->integer('max_gap');
            $table->integer('handicap');
            $table->foreignId('rule_id')
                ->constrained('rules')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('handicap_parameters');
    }
};