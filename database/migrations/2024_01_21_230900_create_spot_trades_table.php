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
        Schema::create('spot_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('binance_trade_id')->nullable();
            $table->foreignId('order_id');
            $table->string('symbol');
            $table->string('order_list_id');
            $table->float('price');
            $table->float('qty');
            $table->float('quote_qty');
            $table->float('commission');
            $table->string('commission_asset');
            $table->string('time');
            $table->boolean('is_buyer')->default(false);
            $table->boolean('is_maker')->default(false);
            $table->boolean('is_best_match')->default(false);
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
        Schema::dropIfExists('spot_trades');
    }
};
