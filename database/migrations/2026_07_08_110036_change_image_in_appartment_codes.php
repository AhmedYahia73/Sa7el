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
        // 1. تنظيف الخانات الفاضية تماماً
        DB::table('appartment_codes')
            ->whereNull('image')
            ->orWhere('image', '')
            ->update(['image' => null]); 

        // 2. تحويل النصوص العادية (مثل image.jpg) إلى صيغة JSON صالحة (مثل "image.jpg")
        // ماريا دي بي تحتاج أن تكون النصوص محاطة بعلامات تنصيص لتعتبر JSON صالح
        DB::table('appartment_codes')
            ->whereNotNull('image')
            ->whereRaw("JSON_VALID(image) = 0") // بجيب السطور اللي الـ JSON فيها مش صالح
            ->update([
                'image' => DB::raw("CONCAT('\"', REPLACE(image, '\"', '\\\\\"'), '\"')")
            ]);

        // 3. الآن يمكنك تعديل نوع العمود بأمان
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
            // تراجع للنوع القديم (غالباً string) لو حبيت تعمل rollback
            $table->string('image')->nullable()->change();
        });
    }
};