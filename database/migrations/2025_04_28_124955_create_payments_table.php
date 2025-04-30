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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('package_id')->nullable()->constrained('packages')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('service_id')->nullable()->constrained('service_types')->onUpdate('cascade')->onDelete('set null');
            $table->float('amount');
            $table->float('discount')->default(0);
            $table->text('rejected_reason')->nullable();
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
        Schema::dropIfExists('payments');
    }
};
