<?php

use App\Models\Article;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('reviewable_type');
            $table->unsignedBigInteger('reviewable_id');
        });
        DB::table('reviews')->whereNotNull('article_id')->update([
            'reviewable_id' => DB::raw('article_id'),
            'reviewable_type' => 'App\Models\Article',
        ]);

        Schema::table('reviews', function (Blueprint $table) {
            $table->renameColumn('user_id', 'author_id');
            $table->dropForeign('reviews_article_id_foreign');
            $table->dropColumn('article_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('article_id')->constrained('articles');
            $table->renameColumn('author_id', 'user_id');
        });

        DB::table('reviews')->where('reviewable_type', 'App\Models\Article')->update([
            'article_id' => DB::raw('reviewable_id'),
        ]);

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['reviewable_id', 'reviewable_type']);
        });
    }
};
