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
            if (!Schema::hasColumn('orders', 'order_type')) {
                $table->string('order_type')->default('dine_in')->after('table_id')->index();
            }

            if (!Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->after('customer_name');
            }

            if (!Schema::hasColumn('orders', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('note')->index();
            }

            if (!Schema::hasColumn('orders', 'delivery_time')) {
                $table->string('delivery_time')->nullable()->after('delivery_date');
            }

            if (!Schema::hasColumn('orders', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('delivery_time');
            }

            if (!Schema::hasColumn('orders', 'catering_status')) {
                $table->string('catering_status')->nullable()->default('pending')->after('status')->index();
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
                'order_type',
                'customer_phone',
                'delivery_date',
                'delivery_time',
                'delivery_address',
                'catering_status',
            ];
            $table->dropColumn(array_intersect($columns, Schema::getColumnListing('orders')));
        });
    }
};
