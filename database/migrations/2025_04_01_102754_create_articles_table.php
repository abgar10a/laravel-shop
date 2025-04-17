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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('name');
            $table->integer('quantity');
            $table->foreignId('type_id')->constrained('types');
            $table->decimal('price', 10, 2);
            $table->double('rating')->default(0);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('color_id')->constrained('colors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
