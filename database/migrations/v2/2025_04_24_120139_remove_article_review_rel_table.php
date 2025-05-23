<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('article_review_rel');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('article_review_rel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles');
            $table->foreignId('review_id')->constrained('reviews');
            $table->timestamps();
        });
    }
};
