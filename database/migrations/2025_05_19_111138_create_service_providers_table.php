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
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_type_id')->nullable()->constrained('maintenance_types')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('phone');
            $table->string('image')->nullable();
            $table->string('location')->nullable();
            $table->string('description')->nullable();            
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->foreignId('package_id')->nullable()->constrained('packages')->onUpdate('cascade')->onDelete('set null');
            $table->boolean('status')->default(1);
            $table->foreignId('village_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->time('open_from')->nullable();
            $table->time('open_to')->nullable();
            $table->string('cover_image')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};
