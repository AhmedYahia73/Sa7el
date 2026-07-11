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
        Schema::create('appartment_type_umbrellas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appartment_type_id')->nullable()->constrained("appartment_types")->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('village_id')->nullable()->constrained("villages")->onUpdate('cascade')->onDelete('cascade');
            $table->integer("umbrellas");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appartment_type_umbrellas');
    }
};
