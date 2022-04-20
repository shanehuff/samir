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
        Schema::table('tradingview_alerts', function (Blueprint $table) {
            $table->unsignedInteger('stochastic')->nullable();
            $table->unsignedInteger('resolution')->nullable();
            $table->string('side')->nullable()->change();
            $table->string('timeframe')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tradingview_alerts', function (Blueprint $table) {
            $table->dropColumn('stochastic');
            $table->dropColumn('resolution');
            $table->string('side')->nullable(false)->change();
            $table->string('timeframe')->nullable(false)->change();
        });
    }
};
