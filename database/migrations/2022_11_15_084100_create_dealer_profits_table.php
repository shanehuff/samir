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
        Schema::create('dealer_profit', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('dealer_id')->unique()->index();
            $table->double('realized_profit');
            $table->double('fee');
            $table->double('net_profit');
            $table->double('roe');
            $table->unsignedBigInteger('duration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealer_profit');
    }
};
