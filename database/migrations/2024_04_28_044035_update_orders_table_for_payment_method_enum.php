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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn('payment_method_id');
            $table->enum('payment_method', ['cash_on_delivery', 'bank_transfer', 'cheque', 'card'])
                  ->default('bank_transfer')
                  ->after('billing_details_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_method_id')->nullable()->after('billing_details_id');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->dropColumn('payment_method');
        });
    }
};
