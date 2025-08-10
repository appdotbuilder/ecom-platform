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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_name')->comment('Product name at time of order');
            $table->string('product_sku')->comment('Product SKU at time of order');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2)->comment('Unit price at time of order');
            $table->decimal('total_price', 15, 2)->comment('Quantity * unit_price');
            $table->json('product_options')->nullable()->comment('Selected options/variants');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
            $table->index(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};