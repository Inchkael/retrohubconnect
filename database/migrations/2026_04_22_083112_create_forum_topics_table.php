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
        if (!Schema::hasTable('forum_topics')) {
            Schema::create('forum_topics', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('forum_id');
                $table->unsignedBigInteger('user_id');
                $table->string('title', 255);
                $table->text('content');
                $table->boolean('is_locked')->default(false);
                $table->boolean('is_pinned')->default(false);
                $table->unsignedInteger('views')->default(0);
                $table->timestamps();

                $table->index('forum_id');
                $table->index('user_id');
                $table->index('is_pinned');
                $table->index('is_locked');

                $table->foreign('forum_id')->references('id')->on('forums')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_topics');
    }
};
