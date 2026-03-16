<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Laravel utilise déjà une table "sessions" par défaut 
        // donc on utilise "class_sessions" pour éviter les conflits
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->boolean('is_active')->default(false);
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
