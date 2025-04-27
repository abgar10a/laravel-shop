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
        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('colors')->insert([
            ['name' => 'Red', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Blue', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Green', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Black', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'White', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yellow', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gray', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pink', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Purple', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Orange', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('color');
    }
};
