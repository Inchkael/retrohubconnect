<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_category_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('service_category_id');
            $table->timestamps();

            // Ajoute les clés étrangères
            //$table->foreign('user_id')
            //    ->references('id')
            //    ->on('users')
            //    ->onDelete('cascade'); // Supprime les enregistrements liés si l'utilisateur est supprimé

            //$table->foreign('service_category_id')
            //    ->references('id')
            //    ->on('service_categories')
            //    ->onDelete('cascade'); // Supprime les enregistrements liés si la catégorie est supprimée
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_category_user');
    }
};
