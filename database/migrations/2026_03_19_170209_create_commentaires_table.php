<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commentaires', function (Blueprint $table) {
            $table->id('CommentaireID'); // Clé primaire auto-incrémentée
            $table->string('Titre', 255);
            $table->text('Contenu');
            $table->tinyInteger('Cote')->default(3);
            $table->dateTime('Encodage')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedBigInteger('InternauteID');
            $table->unsignedBigInteger('PrestataireID');

            // Clé étrangère vers la table `users` (pour l'internaute)
            //$table->unsignedBigInteger('InternauteID');
            //$table->foreign('InternauteID')->references('id')->on('users')->onDelete('cascade');

            // Si `PrestataireID` référence aussi `users` (car prestataire = user avec un rôle spécifique)
            //$table->unsignedBigInteger('PrestataireID');
            //$table->foreign('PrestataireID')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commentaires');
    }
};
