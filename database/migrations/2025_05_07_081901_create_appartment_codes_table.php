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
        Schema::create('appartment_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appartment_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('village_id')->nullable()->constrained('villages')->onUpdate('cascade')->onDelete('cascade');
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->enum('type', ['owner', 'renter'])->default('owner');
            $table->string('code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appartment_codes');
    }
};
