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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->index();
            $table->foreignId('order_id');
            $table->string('side');
            $table->double('price');
            $table->double('qty');
            $table->double('realized_pnl');
            $table->string('margin_asset');
            $table->double('quote_qty');
            $table->double('commission');
            $table->string('commission_asset');
            $table->string('time');
            $table->string('position_side');
            $table->boolean('buyer');
            $table->boolean('maker');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trades');
    }
};
