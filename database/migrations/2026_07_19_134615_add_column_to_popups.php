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
        Schema::table('popups', function (Blueprint $table) {
            $table->enum("gender", ["all", "male", "female"])->default("all");
            $table->integer("age_from")->nullable();
            $table->integer("age_to")->nullable();
            $table->date("start_date");
            $table->date("end_date");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('popups', function (Blueprint $table) {
            //
        });
    }
};
