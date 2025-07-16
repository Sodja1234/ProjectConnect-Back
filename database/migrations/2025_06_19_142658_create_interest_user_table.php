<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('interest_user', function (Blueprint $table) {
            $table->id();

            // Clés étrangères avec 'nullable' pour éviter l'erreur SQLite
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('interest_id')->nullable();

            // Index pour les relations
            $table->index(['user_id', 'interest_id']);

            // Contrainte de clé étrangère manuelle (compatible SQLite)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('interest_id')->references('id')->on('interests')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interest_user');
    }
};
