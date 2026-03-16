<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_participants', function (Blueprint $table) {
            $table->id();
            // Relation polymorphique : peut pointer vers "players" ou "admin_users"
            $table->string('participantable_type');
            $table->string('participantable_id');
            $table->decimal('elo_rating', 8, 2)->default(100);
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->timestamps();

            // Index pour la relation polymorphique
            $table->index(['participantable_type', 'participantable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_participants');
    }
};
