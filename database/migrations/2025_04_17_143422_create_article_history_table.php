<?php

use App\Enums\ArticleProcessType;
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
        Schema::create('article_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('action', array_map(fn($case) => $case->value, ArticleProcessType::cases()));
            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->integer('quantity')->default(0);
            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_history');
    }
};
