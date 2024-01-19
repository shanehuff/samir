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
        Schema::create('spot_orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->string('symbol')->index();
            $table->unsignedTinyInteger('status');
            $table->string('client_order_id')->unique()->index();
            $table->string('transact_time');
            $table->float('price');
            $table->float('orig_qty');
            $table->float('executed_qty');
            $table->float('cummulative_quote_qty');
            $table->string('time_in_force');
            $table->string('type')->index();
            $table->string('side')->index();
            $table->string('working_time');
            $table->string('fills');
            $table->string('self_trade_prevention_mode');
            $table->foreignId('champion_id');
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
        Schema::dropIfExists('spot_orders');
    }
};
