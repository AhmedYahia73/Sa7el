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
        Schema::table('user_village', function (Blueprint $table) {
            $table->enum('type', ['owner', 'rent'])->default('owner');
            $table->date('rent_from')->nullable();
            $table->date('rent_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_village', function (Blueprint $table) {
            //
        });
    }
};
