<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'is_reported')) {
                $table->boolean('is_reported')->default(false);
            }
            if (!Schema::hasColumn('reviews', 'report_reason')) {
                $table->string('report_reason')->nullable();
            }
            if (!Schema::hasColumn('reviews', 'report_comment')) {
                $table->text('report_comment')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'is_reported')) {
                $table->dropColumn('is_reported');
            }
            if (Schema::hasColumn('reviews', 'report_reason')) {
                $table->dropColumn('report_reason');
            }
            if (Schema::hasColumn('reviews', 'report_comment')) {
                $table->dropColumn('report_comment');
            }
        });
    }
};
