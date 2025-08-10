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
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reseller_id')->nullable()->comment('POS transaction reseller');
            $table->enum('order_type', ['online', 'pos'])->default('online');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded', 'hutang'])->default('pending');
            $table->enum('payment_method', ['midtrans', 'xendit', 'bank_transfer', 'hutang', 'cash'])->nullable();
            $table->string('payment_reference')->nullable();
            $table->json('shipping_address');
            $table->json('billing_address')->nullable();
            $table->string('shipping_service')->nullable()->comment('JNE, TIKI, etc');
            $table->string('shipping_service_type')->nullable()->comment('REG, YES, etc');
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->json('payment_data')->nullable()->comment('Payment gateway response');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('reseller_id')->references('id')->on('users');
            $table->index(['user_id', 'status']);
            $table->index(['reseller_id', 'order_type']);
            $table->index('order_number');
            $table->index(['status', 'created_at']);
            $table->index('payment_status');
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