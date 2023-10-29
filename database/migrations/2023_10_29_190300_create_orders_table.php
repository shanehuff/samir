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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->string('symbol')->index();
            $table->unsignedTinyInteger('status');
            $table->string('client_order_id')->unique()->index();
            $table->float('price');
            $table->float('avg_price');
            $table->float('orig_qty');
            $table->float('executed_qty');
            $table->float('cum_qty');
            $table->float('cum_quote');
            $table->string('time_in_force');
            $table->string('type')->index();
            $table->boolean('reduce_only');
            $table->boolean('close_position');
            $table->string('side')->index();
            $table->string('position_side')->index();
            $table->float('stop_price');
            $table->string('working_type');
            $table->boolean('price_protect');
            $table->string('orig_type');
            $table->string('price_match');
            $table->string('self_trade_prevention_mode');
            $table->string('good_till_date');
            $table->string('update_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
