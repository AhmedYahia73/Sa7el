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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('village_id')->after('service_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('provider_id')->after('service_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->date('start_date')->after('service_id')->nullable();
            $table->date('expire_date')->after('service_id')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
};
