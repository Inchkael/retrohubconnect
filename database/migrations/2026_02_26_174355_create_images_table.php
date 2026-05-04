<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('format');
            $table->string('type'); // 'original', '380w', '540w', '700w'
            $table->unsignedInteger('position')->default(0);
            $table->unsignedBigInteger('imageable_id');
            $table->string('imageable_type');
            $table->timestamps();

            // Créer l'index uniquement s'il n'existe pas déjà
            if (!Schema::hasIndex('images', 'images_imageable_type_imageable_id_index')) {
                $table->index(['imageable_type', 'imageable_id'], 'images_imageable_type_imageable_id_index');
            }

            $table->index('position');
        });
    }

    public function down()
    {
        Schema::dropIfExists('images');
    }
};
