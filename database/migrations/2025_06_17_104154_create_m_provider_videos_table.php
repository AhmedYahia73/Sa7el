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
        Schema::create('m_provider_videos', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->string('video')->nullable();
            $table->boolean('status')->default(1);
            $table->foreignId('m_provider_id')->nullable()->constrained('service_providers')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_provider_videos');
    }
};
