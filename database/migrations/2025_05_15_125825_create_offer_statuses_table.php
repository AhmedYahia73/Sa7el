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
        Schema::create('offer_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appartment_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->enum('status_offer', ['rent', 'sale', 'other'])->default('other');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_statuses');
    }
};
