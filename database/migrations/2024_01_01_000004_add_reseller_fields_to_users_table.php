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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('reseller_level_id')->nullable()->after('email_verified_at');
            $table->unsignedBigInteger('upline_id')->nullable()->after('reseller_level_id')->comment('Parent reseller');
            $table->decimal('total_sales', 15, 2)->default(0)->after('upline_id');
            $table->decimal('total_commission_earned', 15, 2)->default(0)->after('total_sales');
            $table->boolean('is_affiliate')->default(false)->after('total_commission_earned');
            $table->string('affiliate_code')->nullable()->unique()->after('is_affiliate');
            $table->text('address')->nullable()->after('affiliate_code');
            $table->string('phone')->nullable()->after('address');
            $table->enum('user_type', ['customer', 'reseller', 'admin'])->default('customer')->after('phone');

            $table->foreign('reseller_level_id')->references('id')->on('reseller_levels');
            $table->foreign('upline_id')->references('id')->on('users');
            $table->index(['upline_id', 'user_type']);
            $table->index('affiliate_code');
            $table->index(['user_type', 'is_affiliate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['reseller_level_id']);
            $table->dropForeign(['upline_id']);
            $table->dropColumn([
                'reseller_level_id',
                'upline_id',
                'total_sales',
                'total_commission_earned',
                'is_affiliate',
                'affiliate_code',
                'address',
                'phone',
                'user_type'
            ]);
        });
    }
};