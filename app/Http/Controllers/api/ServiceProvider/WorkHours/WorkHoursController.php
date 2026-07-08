<?php

namespace App\Http\Controllers\api\ServiceProvider\WorkHours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Provider;
use App\Models\ProviderWorkHours;

class WorkHoursController extends Controller
{
    const DAYS = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

    public function __construct(private Provider $provider) {}

    public function view(Request $request)
    {
        $provider = $this->provider
            ->with('work_hours')
            ->where('id', $request->user()->provider_id)
            ->first();

        return response()->json([
            'work_hours' => $provider?->work_hours,
            'is_open_now' => $provider?->isOpenNow(),
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'work_hours'              => 'required|array',
            'work_hours.*.day'        => 'required|in:' . implode(',', self::DAYS),
            'work_hours.*.from'       => 'nullable|date_format:H:i:s',
            'work_hours.*.to'         => 'nullable|date_format:H:i:s',
            'work_hours.*.is_24_hours'=> 'boolean',
            'work_hours.*.is_closed'  => 'boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $provider_id = $request->user()->provider_id;

        foreach ($request->work_hours as $item) {
            ProviderWorkHours::updateOrCreate(
                ['provider_id' => $provider_id, 'day' => $item['day']],
                [
                    'from'        => $item['is_24_hours'] ?? false ? null : ($item['from'] ?? null),
                    'to'          => $item['is_24_hours'] ?? false ? null : ($item['to'] ?? null),
                    'is_24_hours' => $item['is_24_hours'] ?? false,
                    'is_closed'   => $item['is_closed'] ?? false,
                ]
            );
        }

        return response()->json(['success' => 'You update data success']);
    }
}
