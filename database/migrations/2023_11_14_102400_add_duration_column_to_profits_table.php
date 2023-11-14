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
        Schema::table('profits', function (Blueprint $table) {
            $table->unsignedBigInteger('duration')->nullable();
            $table->string('duration_readable')->nullable();
            $table->float('buy_price')->nullable();
            $table->float('sell_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('profits', function (Blueprint $table) {
            $table->dropColumn('duration');
            $table->dropColumn('duration_readable');
            $table->dropColumn('buy_price');
            $table->dropColumn('sell_price');
        });
    }
};
