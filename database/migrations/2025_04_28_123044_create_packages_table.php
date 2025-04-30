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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained('service_types')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->float('price');
            $table->float('feez')->default(0);
            $table->float('discount')->default(0);
            $table->enum('type', ['provider', 'village']);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
