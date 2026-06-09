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
        Schema::create('code_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('appartment_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('code');
            $table->enum("status", ["pending", "approve", "reject"])->default("pending");
            $table->json('appartment_codes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_requests');
    }
};
