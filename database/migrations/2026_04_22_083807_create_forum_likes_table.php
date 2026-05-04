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
        if (!Schema::hasTable('forum_likes')) {
            Schema::create('forum_likes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('reply_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                $table->index('reply_id');
                $table->index('user_id');

                $table->foreign('reply_id')->references('id')->on('forum_replies')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                $table->unique(['reply_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_likes');
    }
};
