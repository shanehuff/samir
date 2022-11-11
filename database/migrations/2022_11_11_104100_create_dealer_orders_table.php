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
        Schema::create('dealer_orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('dealer_id');
            $table->foreignId('binance_order_id')->unique()->index();
            $table->string('binance_client_id')->unique()->index();
            $table->float('price');
            $table->float('size');
            $table->unsignedTinyInteger('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealer_orders');
    }
};
