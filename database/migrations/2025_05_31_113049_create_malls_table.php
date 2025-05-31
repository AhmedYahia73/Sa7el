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
        Schema::create('malls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->time('open_from')->nullable();
            $table->time('open_to')->nullable();
            $table->string('image')->nullable();
            $table->string('cover_image')->nullable();
            $table->foreignId('zone_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('malls');
    }
};
