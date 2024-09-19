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
        Schema::create('comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article1_id')->constrained('news_articles')->onDelete('cascade');
            $table->foreignId('article2_id')->constrained('news_articles')->onDelete('cascade');
            $table->decimal('similarity', 5, 2);  // e.g., 85.75%
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comparisons');
    }
};
