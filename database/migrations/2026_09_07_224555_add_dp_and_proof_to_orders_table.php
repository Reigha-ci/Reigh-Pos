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
            if (!Schema::hasColumn('orders', 'dp_percentage')) {
                $table->integer('dp_percentage')->nullable()->default(50)->after('total_price');
            }
            if (!Schema::hasColumn('orders', 'dp_amount')) {
                $table->decimal('dp_amount', 12, 2)->nullable()->default(0)->after('dp_percentage');
            }
            if (!Schema::hasColumn('orders', 'remaining_amount')) {
                $table->decimal('remaining_amount', 12, 2)->nullable()->default(0)->after('dp_amount');
            }
            if (!Schema::hasColumn('orders', 'remaining_payment_method')) {
                $table->string('remaining_payment_method')->nullable()->default('cod')->after('remaining_amount');
            }
            if (!Schema::hasColumn('orders', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('remaining_payment_method');
            }
            if (!Schema::hasColumn('orders', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('payment_proof');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'dp_percentage',
                'dp_amount',
                'remaining_amount',
                'remaining_payment_method',
                'payment_proof',
                'rejection_reason',
            ];
            $table->dropColumn(array_intersect($columns, Schema::getColumnListing('orders')));
        });
    }
};
