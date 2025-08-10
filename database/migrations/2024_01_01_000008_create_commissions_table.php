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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('reseller_id');
            $table->unsignedBigInteger('from_user_id')->comment('User who made the purchase');
            $table->integer('level')->comment('Commission level (1-10)');
            $table->decimal('order_amount', 15, 2);
            $table->decimal('commission_percentage', 5, 2);
            $table->decimal('commission_amount', 15, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->enum('type', ['reseller', 'affiliate'])->default('reseller');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('reseller_id')->references('id')->on('users');
            $table->foreign('from_user_id')->references('id')->on('users');
            $table->index(['reseller_id', 'status']);
            $table->index(['order_id', 'level']);
            $table->index(['from_user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};