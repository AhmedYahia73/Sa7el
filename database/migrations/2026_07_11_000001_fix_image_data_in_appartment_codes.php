<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix rows where image is a plain string (not valid JSON)
        $rows = DB::table('appartment_codes')
            ->whereNotNull('image')
            ->get(['id', 'image']);

        foreach ($rows as $row) {
            $decoded = json_decode($row->image, true);

            // If json_decode failed or returned a plain string (not array), fix it
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                $fixed = json_encode(
                    !empty($row->image) ? [$row->image] : []
                );
                DB::table('appartment_codes')
                    ->where('id', $row->id)
                    ->update(['image' => $fixed]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
