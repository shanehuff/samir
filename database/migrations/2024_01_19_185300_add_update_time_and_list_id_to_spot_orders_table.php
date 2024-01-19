<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('spot_orders', function (Blueprint $table) {
            $table->string('order_list_id')->nullable();
            $table->string('update_time')->nullable();
            $table->string('orig_quote_order_qty')->nullable();
            $table->boolean('is_working')->nullable();
            $table->string('iceberg_qty')->nullable();
            $table->float('stop_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('spot_orders', function (Blueprint $table) {
            $table->dropColumn('order_list_id');
            $table->dropColumn('update_time');
            $table->dropColumn('orig_quote_order_qty');
            $table->dropColumn('is_working');
            $table->dropColumn('iceberg_qty');
            $table->dropColumn('stop_price');
        });
    }
};
