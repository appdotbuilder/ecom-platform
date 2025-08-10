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
        Schema::create('reseller_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level');
            $table->decimal('discount_percentage', 5, 2)->comment('Discount from base price');
            $table->decimal('commission_percentage', 5, 2)->comment('Commission from sales');
            $table->decimal('min_sales_amount', 15, 2)->default(0)->comment('Minimum sales to maintain level');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('level');
            $table->index(['level', 'is_active']);
            $table->index('min_sales_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_levels');
    }
};