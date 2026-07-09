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
        Schema::create('appartment_code_rent_image', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appartment_code_id')->nullable()->constrained("appartment_codes")->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('rent_image_id')->nullable()->constrained("rent_images")->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appartment_code_rent_image');
    }
};
