<?php

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
        Schema::create('gallery_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        $gallery_categories = [
            ['name' => 'Living Room Interior'],
            ['name' => 'Kitchen Interior'],
            ['name' => 'Room Interior'],
            ['name' => 'Office Interior']
        ];

        foreach ($gallery_categories as $gallery_category) {
            DB::table('gallery_categories')->insert([
                'name' => $gallery_category['name'],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_categories');
    }
};
