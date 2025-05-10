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
        Schema::create('maintenance_type_villages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_types_id')->nullable()->constrained('maintenance_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('village_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_type_villages');
    }
};
