<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('news_articles', function (Blueprint $table) {
            $table->text('sentiment')->nullable(); // Store the sentiment as JSON or text
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('news_articles', function (Blueprint $table) {
            $table->dropColumn('sentiment');
        });
    }
};
