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
            $table->string('fills')->nullable()->change();
            $table->string('transact_time')->nullable()->change();
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
            $table->string('fills')->change();
            $table->string('transact_time')->change();
        });
    }
};
