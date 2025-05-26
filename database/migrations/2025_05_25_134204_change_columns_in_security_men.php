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
        Schema::table('security_men', function (Blueprint $table) {
            $table->dropColumn('shift_from');
            $table->dropColumn('shift_to');
            $table->dropColumn('location');
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('security_men', function (Blueprint $table) {
            //
        });
    }
};
