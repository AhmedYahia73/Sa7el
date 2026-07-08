<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('appartment_codes')
            ->whereNull('image')
            ->orWhere('image', '')
            ->update(['image' => null]); 
            
        Schema::table('appartment_codes', function (Blueprint $table) {
            $table->json('image')->nullable()->change();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appartment_codes', function (Blueprint $table) {
            //
        });
    }
};
