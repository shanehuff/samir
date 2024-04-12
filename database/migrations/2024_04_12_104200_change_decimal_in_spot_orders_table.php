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
            $table->decimal('orig_qty', 30, 12)->change();
            $table->decimal('executed_qty', 30, 12)->change();
            $table->decimal('cummulative_quote_qty', 30, 12)->change();
            $table->decimal('price', 30, 12)->change();
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
            $table->float('orig_qty')->change();
            $table->float('executed_qty')->change();
            $table->float('cummulative_quote_qty')->change();
            $table->float('price')->change();
        });
    }
};
