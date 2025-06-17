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
        Schema::create('m_provider_offers', function (Blueprint $table) {
            $table->id();
            $table->string('description', 500)->nullable();
            $table->string('image')->nullable();
            $table->foreignId('m_provider_id')->nullable()->constrained('service_providers')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_provider_offers');
    }
};
