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
        Schema::table('appartment_codes', function (Blueprint $table) {
            $table->enum('user_type', ['follower', 'super'])->default('follower');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appartment_codes', function (Blueprint $table) {
            //
        });
    }
};
