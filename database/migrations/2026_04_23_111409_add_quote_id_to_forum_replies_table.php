<?php
// database/migrations/[timestamp]_add_quote_id_to_forum_replies_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('forum_replies', function (Blueprint $table) {
            $table->unsignedBigInteger('quote_id')->nullable()->after('topic_id');
            $table->foreign('quote_id')->references('id')->on('forum_replies')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('forum_replies', function (Blueprint $table) {
            $table->dropForeign(['quote_id']);
            $table->dropColumn('quote_id');
        });
    }
};
