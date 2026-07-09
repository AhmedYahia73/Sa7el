<?php

namespace App\Http\Controllers\api\SuperAdmin\notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use App\Events\UserNotification;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function push_notification(Request $request){
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'village_id' => 'sometimes|nullable|exists:villages,id',
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $usersQuery = User::where("role", "user");

        if ($request->village_id) {
            $usersQuery->whereHas("appartment_code", function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('type', 'owner')
                    ->where('village_id', $request->village_id);
                })->orWhere(function($q) use ($request) {
                    $q->where('type', 'renter')
                    ->where('village_id', $request->village_id)
                    ->where('from', '<=', now()->format('Y-m-d'))
                    ->where('to', '>=', now()->format('Y-m-d'));
                });
            });
        }

        $users = $usersQuery->get();
        $notificationText = $request->message;

        foreach ($users as $user) {
            $data = [ 
                'village_id' => $request->village_id ?? null,
                'code_request_id' => null,
                'login_request_id' => null,
                "type" => "user", 
                'notification' => $notificationText,
                'is_read' => 0,
                "user_id" => $user->id,
            ];

            // بث الحدث عبر Reverb
            UserNotification::dispatch($data);
            
            // حفظ الإشعار في قاعدة البيانات
            $new_notification = Notification::create($data);

            // تجهيز البيانات لـ Firebase والـ Broadcast معاً
            $notificationData = [
                "id" => $new_notification->id,
                "title" => "تنبيه هام",
                "body" => $notificationText
            ];

            // إرسال الإشعار للمستخدم الحالي (تم حل مشكلة الفايند والـ $codes المعطوب)
            $user->notify(new NotificationChanged($notificationData));
        }

        return response()->json([
            'message' => 'Notifications sent successfully to ' . $users->count() . ' users.'
        ]);
    }
}
