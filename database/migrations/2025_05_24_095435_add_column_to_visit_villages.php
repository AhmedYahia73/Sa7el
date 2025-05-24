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
            $table->foreignId('appartment_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->enum('visitor_type', ['guest', 'worker', 'delivery'])->nullable();
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
