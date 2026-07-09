<?php

namespace App\Http\Controllers\api\Village\notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Jobs\SendPushNotificationJob;
use Illuminate\Support\Facades\Validator;

class PushNotificationController extends Controller
{
    public function push_notification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $village_id = $request->user()->village_id;

        $users = User::where('role', 'user')
            ->whereHas('appartment_code', function ($query) use ($village_id) {
                $query->where(function ($q) use ($village_id) {
                    $q->where('type', 'owner')
                        ->where('village_id', $village_id);
                })->orWhere(function ($q) use ($village_id) {
                    $q->where('type', 'renter')
                        ->where('village_id', $village_id)
                        ->where('from', '<=', now()->format('Y-m-d'))
                        ->where('to', '>=', now()->format('Y-m-d'));
                });
            })
            ->get();

        foreach ($users as $user) {
            SendPushNotificationJob::dispatch($user, $request->message, $village_id);
        }

        return response()->json([
            'message' => 'Notifications queued successfully to ' . $users->count() . ' users.',
        ]);
    }
}
