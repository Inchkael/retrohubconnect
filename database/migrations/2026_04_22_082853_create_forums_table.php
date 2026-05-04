<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Limite à 100 caractères pour le nom
            $table->text('description')->nullable(); // Description
            $table->unsignedBigInteger('category_id')->nullable(); // Pour catégorisation
            $table->timestamps();

            // Index pour les colonnes fréquemment interrogées
            $table->index('name');
            $table->index('category_id');

            // Clé étrangère des catégories
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forums');
    }
};
