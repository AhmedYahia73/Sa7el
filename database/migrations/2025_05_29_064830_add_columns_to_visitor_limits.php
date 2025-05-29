<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('visitor_limits', function (Blueprint $table) {
            $table->integer('renter_guest');
            $table->integer('renter_worker');
            $table->integer('renter_delivery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitor_limits', function (Blueprint $table) {
            //
        });
    }
};
