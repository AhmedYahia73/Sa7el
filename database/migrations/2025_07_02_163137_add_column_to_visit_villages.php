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
        Schema::table('visit_villages', function (Blueprint $table) {
            $table->enum('user_type', ['owner', 'renter']);
            $table->enum('visitor_type', ['guest', 'worker', 'delivery', 'owner'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_villages', function (Blueprint $table) {
            //
        });
    }
};
