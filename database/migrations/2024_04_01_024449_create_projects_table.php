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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('images');
            $table->string('description')->nullable();
            $table->string('architect')->nullable();
            $table->string('designer')->nullable();
            $table->string('concept')->nullable();
            $table->string('location')->nullable();
            $table->date('date')->nullable();
            $table->string('link')->nullable();

            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('visibility')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
