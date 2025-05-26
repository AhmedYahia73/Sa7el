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
        Schema::create('security_position', function (Blueprint $table) {
            $table->id();
            $table->foreignId('security_id')->nullable()->constrained('security_men')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('pool_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('beach_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('gate_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_position');
    }
};
