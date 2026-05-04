<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('address_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('original_address');
            $table->string('clean_address')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamps();

            // Index pour améliorer les performances de recherche
            $table->index('original_address');
        });
    }

    public function down()
    {
        Schema::dropIfExists('address_mappings');
    }
};
