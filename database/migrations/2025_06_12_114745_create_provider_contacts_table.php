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
        Schema::create('provider_contacts', function (Blueprint $table) {
            $table->id();
            $table->boolean('watts_status')->default(1);
            $table->boolean('phone_status')->default(1);
            $table->boolean('website_status')->default(1);
            $table->boolean('instagram_status')->default(1);
            $table->string('watts')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('instagram')->nullable();
            $table->foreignId('provider_id')->nullable()->constrained('service_providers')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_contacts');
    }
};
