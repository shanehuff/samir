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
        Schema::table('champions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable();
            $table->string('status')->nullable();
            $table->double('profit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('champions', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('status');
            $table->dropColumn('profit');
        });
    }
};
