<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('types', function (Blueprint $table) {
            $table->renameColumn('name', 'title');
            $table->string('identifier')->unique();
        });

        $types = DB::table('types')->get();

        foreach ($types as $type) {
            $identifier = Str::slug($type->title, '_');
            DB::table('types')->where('id', $type->id)->update(['identifier' => $identifier]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('types', function (Blueprint $table) {
            $table->renameColumn('title', 'name');
            $table->dropColumn('identifier');
        });
    }
};
