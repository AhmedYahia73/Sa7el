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
        Schema::create('inside_security', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gate_id')->nullable()->constrained("inside_gates")->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('security_id')->nullable()->constrained("security_men")->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inside_security');
    }
};
