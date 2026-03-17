<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('class_participants', function (Blueprint $table) {
            $table->id();
            
            $table->string('participantable_type');
            $table->uuid('participantable_id');
            $table->decimal('elo_rating', 5, 2)->default(100);
            $table->timestamps();
            
            $table->foreignId('school_class_id')
                ->constrained('school_classes')
                ->onDelete('restrict');
            
            // Index pour la relation polymorphique
            $table->index(['participantable_type', 'participantable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_participants');
    }
};
