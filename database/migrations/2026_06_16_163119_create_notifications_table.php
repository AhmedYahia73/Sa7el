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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('code_request_id')->nullable()->constrained("code_requests")->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('login_request_id')->nullable()->constrained("login_requests")->onUpdate('cascade')->onDelete('cascade');
            $table->enum("type", ["user", "admin"]);
            $table->string("notification");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
