<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_work_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            // day: monday, tuesday, wednesday, thursday, friday, saturday, sunday
            $table->string('day');
            $table->time('from')->nullable();
            $table->time('to')->nullable();
            $table->boolean('is_24_hours')->default(0);
            $table->boolean('is_closed')->default(0);
            $table->timestamps();

            $table->unique(['provider_id', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_work_hours');
    }
};
