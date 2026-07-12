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
        Schema::create('zone_villages', function (Blueprint $table) {
            $table->id();
            $table->json("name");
            $table->json("description")->nullable();
            $table->decimal("lat");
            $table->decimal("lng");
            $table->foreignId('village_id')->nullable()->constrained("villages")->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zone_villages');
    }
};
