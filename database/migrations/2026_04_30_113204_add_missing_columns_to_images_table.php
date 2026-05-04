<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            // Ajouter les colonnes manquantes si elles n'existent pas
            if (!Schema::hasColumn('images', 'original_name')) {
                $table->string('original_name')->nullable()->after('path');
            }
            if (!Schema::hasColumn('images', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('original_name');
            }
            if (!Schema::hasColumn('images', 'size')) {
                $table->unsignedBigInteger('size')->nullable()->after('mime_type');
            }
            if (!Schema::hasColumn('images', 'format')) {
                $table->string('format')->nullable()->after('size');
            }
            if (!Schema::hasColumn('images', 'type')) {
                $table->string('type')->nullable()->after('format');
            }
            if (!Schema::hasColumn('images', 'position')) {
                $table->unsignedInteger('position')->nullable()->after('type');
            }
        });
    }

    public function down()
    {
        Schema::table('images', function (Blueprint $table) {
            // Vous pouvez supprimer les colonnes si nécessaire
            // $table->dropColumn(['original_name', 'mime_type', 'size', 'format', 'type', 'position']);
        });
    }
};
