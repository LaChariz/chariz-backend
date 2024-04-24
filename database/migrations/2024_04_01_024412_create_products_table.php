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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_image');
            $table->integer('price');
            $table->string('description');
            $table->integer('quantity')->nullable();
            $table->integer('sold_items')->nullable();
            $table->integer('sales_price')->nullable();
            $table->json('images')->nullable();
            $table->string('additional_info')->nullable();
            $table->string('sku')->nullable();
            $table->string('weight')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('shipping_method')->nullable();
            $table->integer('shipping_cost')->nullable();
            $table->string('shipping_time')->nullable();
            $table->string('location')->nullable();

            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
