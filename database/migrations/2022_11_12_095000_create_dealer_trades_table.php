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
    public function up()
    {
        Schema::create('dealer_trades', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('dealer_id');
            $table->foreignId('binance_id')->unique()->index();
            $table->string('symbol');
            $table->foreignId('binance_order_id');
            $table->string('side');
            $table->double('price');
            $table->double('size');
            $table->double('realized_pnl');
            $table->string('pnl_asset');
            $table->double('total');
            $table->double('fee');
            $table->string('fee_asset');
            $table->unsignedBigInteger('binance_timestamp');
            $table->string('position_side');
            $table->boolean('buyer');
            $table->boolean('maker');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealer_trades');
    }
};
