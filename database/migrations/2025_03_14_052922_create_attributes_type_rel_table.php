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
        Schema::create('attributes_type_rel', function (Blueprint $table) {
            $table->id();
            $table->integer('type_id');
            $table->integer('attribute_id');
            $table->timestamps();
        });

        DB::table('attributes_type_rel')->insert([
            // Laptop
            ['attribute_id' => 1, 'type_id' => 1], // Processor
            ['attribute_id' => 2, 'type_id' => 1], // RAM
            ['attribute_id' => 3, 'type_id' => 1], // Storage
            ['attribute_id' => 4, 'type_id' => 1], // Screen Size
            ['attribute_id' => 5, 'type_id' => 1], // Graphics Card

            // Phone
            ['attribute_id' => 6, 'category_id' => 2], // Camera (MP)
            ['attribute_id' => 7, 'category_id' => 2], // Battery Capacity (mAh)
            ['attribute_id' => 8, 'category_id' => 2], // Operating System
            ['attribute_id' => 9, 'category_id' => 2], // SIM Slots

            // Headphone
            ['attribute_id' => 10, 'category_id' => 3], // Wireless
            ['attribute_id' => 11, 'category_id' => 3], // Noise Cancellation
            ['attribute_id' => 12, 'category_id' => 3], // Battery Life (hours)
            ['attribute_id' => 13, 'category_id' => 3], // Connector Type

            // Display
            ['attribute_id' => 14, 'category_id' => 4], // Resolution
            ['attribute_id' => 15, 'category_id' => 4], // Refresh Rate
            ['attribute_id' => 16, 'category_id' => 4], // Maximum Resolution
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes_type_rel');
    }
};
