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
        Schema::create('security_men', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('location');
            $table->time('shift_from');
            $table->time('shift_to');
            $table->string('password');
            $table->string('image')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->enum('type', ['pool', 'gate', 'beach']);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_men');
    }
};
