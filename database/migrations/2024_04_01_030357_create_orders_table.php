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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('billing_details_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('billing_details_id')->references('id')->on('billing_details');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['ongoing', 'delivered', 'cancelled', 'returned'])->default('ongoing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
