<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('types')->insert([
            ['name' => 'Laptops', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Phones', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Headphones', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Displays', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type');
    }
};
