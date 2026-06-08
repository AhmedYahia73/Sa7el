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
        Schema::table('appartments', function (Blueprint $table) {
            $table->boolean('entrance_status')->default(0);
            $table->boolean('pool_status')->default(0);
            $table->boolean('beach_status')->default(0);
            $table->boolean('rent_code_status')->default(0);
            $table->boolean('selling_status')->default(0);
            $table->boolean('rent_status')->default(0);
            $table->boolean('visits_status')->default(0);
            $table->boolean('options_status')->default(0);
            $table->boolean('all_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appartments', function (Blueprint $table) {
            //
        });
    }
};
