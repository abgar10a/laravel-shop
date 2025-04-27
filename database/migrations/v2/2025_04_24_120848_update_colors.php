<?php

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
        Schema::table('colors', function (Blueprint $table) {
            $table->string('color_code');
        });

        $colorMap = [
            'Red' => '#FF0000',
            'Green' => '#00FF00',
            'Blue' => '#0000FF',
            'Yellow' => '#FFFF00',
            'Black' => '#000000',
            'White' => '#FFFFFF',
            'Gray' => '#808080',
            'Orange' => '#FFA500',
            'Purple' => '#800080',
            'Pink' => '#FFC0CB',
        ];

        foreach ($colorMap as $name => $hex) {
            DB::table('colors')
                ->where('name', $name)
                ->update(['color_code' => $hex]);
        }

        Schema::create('article_color', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles');
            $table->foreignId('color_id')->constrained('colors');
            $table->timestamps();
        });

        $articles = DB::table('articles')->select('id', 'color_id')->get();

        foreach ($articles as $article) {
            DB::table('article_color')->insert([
                'article_id' => $article->id,
                'color_id' => $article->color_id,
            ]);
        }

        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign('articles_color_id_foreign');
            $table->dropColumn('color_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('color_id')->constrained('colors');
        });

        $articleColors = DB::table('article_color')->get();

        foreach ($articleColors as $entry) {
            DB::table('articles')
                ->where('id', $entry->article_id)
                ->update(['color_id' => $entry->color_id]);
        }

        Schema::dropIfExists('article_color');

        Schema::table('colors', function (Blueprint $table) {
            $table->dropColumn('color_code');
        });
    }
};
