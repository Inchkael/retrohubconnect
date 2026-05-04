<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // 1. D'abord ajouter la nouvelle colonne reviewable_type
            $table->string('reviewable_type')->nullable()->after('user_id');

            // 2. Renommer item_id en reviewable_id
            $table->renameColumn('item_id', 'reviewable_id');
        });

        // 3. Mettre à jour reviewable_type pour les enregistrements existants
        // DOIT ÊTRE FAIT APRÈS la création de la colonne
        DB::table('reviews')->update(['reviewable_type' => 'App\Models\Item']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // 1. Renommer reviewable_id en item_id
            $table->renameColumn('reviewable_id', 'item_id');

            // 2. Supprimer la colonne reviewable_type
            $table->dropColumn('reviewable_type');
        });
    }
};
