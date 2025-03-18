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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('identifier');
            $table->string('title');
            $table->timestamps();
        });

        DB::table('attributes')->insert([
            // Laptop attributes
            ['identifier' => 'processor', 'title' => 'Processor', 'created_at' => now(), 'updated_at' => now()],
            ['identifier' => 'ram', 'title' => 'RAM', 'created_at' => now(), 'updated_at' => now()],
            ['identifier' => 'storage', 'title' => 'Storage', 'created_at' => now(), 'updated_at' => now()],
            ['identifier' => 'screen_size', 'title' => 'Screen Size', 'created_at' => now(), 'updated_at' => now()],
            ['identifier' => 'gpu', 'title' => 'Graphics Card', 'created_at' => now(), 'updated_at' => now()],

            // Phone attributes
            ['identifier' => 'camera_megapixels', 'title' => 'Camera (MP)', 'created_at' => now(), 'updated_at' => now()],
            ['identifier' => 'battery_capacity', 'title' => 'Battery Capacity (mAh)', 'created_at' => now(), 'updated_at' => now()],
            ['identifier' => 'os', 'title' => 'Operating System', 'created_at' => now(), 'updated_at' => now()],
            ['identifier' => 'sim_slots', 'title' => 'SIM Slots', 'created_at' => now(), 'updated_at' => now()],

            // Headphone attributes
            ['identifier' => 'wireless', 'title' => 'Wireless', 'created_at' => now(), 'updated_at' => now()],
            ['identifier' => 'noise_cancellation', 'title' => 'Noise Cancellation', 'created_at' => now(), 'updated_at' => now()],
            ['identifier' => 'battery_life_hours', 'title' => 'Battery Life (hours)', 'created_at' => now(), 'updated_at' => now()],
            ['identifier' => 'connector_type', 'title' => 'Connector Type', 'created_at' => now(), 'updated_at' => now()],

            // Display attributes
            ['identifier' => 'resolution', 'title' => 'Resolution', 'created_at' => now(), 'updated_at' => now()],
            ['identifier' => 'refresh_rate', 'title' => 'Refresh Rate', 'created_at' => now(), 'updated_at' => now()],
            ['identifier' => 'maximum_resolution', 'title' => 'Maximum Resolution', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
