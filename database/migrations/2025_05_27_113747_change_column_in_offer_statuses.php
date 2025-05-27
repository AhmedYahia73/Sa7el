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
        Schema::table('offer_statuses', function (Blueprint $table) {
            $table->dropColumn('status_offer');
            $table->boolean('rent_status')->default(0);
            $table->boolean('sale_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_statuses', function (Blueprint $table) {
            //
        });
    }
};
