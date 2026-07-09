<?php

namespace App\Http\Controllers\api\SuperAdmin\notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Jobs\SendPushNotificationJob;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function push_notification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message'    => 'required|string',
            'village_id' => 'sometimes|nullable|exists:villages,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $usersQuery = User::where('role', 'user');

        if ($request->village_id) {
            $usersQuery->whereHas('appartment_code', function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('type', 'owner')
                        ->where('village_id', $request->village_id);
                })->orWhere(function ($q) use ($request) {
                    $q->where('type', 'renter')
                        ->where('village_id', $request->village_id)
                        ->where('from', '<=', now()->format('Y-m-d'))
                        ->where('to', '>=', now()->format('Y-m-d'));
                });
            });
        }

        $users = $usersQuery->get();

        foreach ($users as $user) {
            SendPushNotificationJob::dispatch($user, $request->message, $request->village_id);
        }

        return response()->json([
            'message' => 'Notifications queued successfully to ' . $users->count() . ' users.',
        ]);
    }
}
