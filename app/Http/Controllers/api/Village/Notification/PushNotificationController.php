<?php

namespace App\Http\Controllers\api\Village\notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Jobs\SendPushNotificationJob;
use Illuminate\Support\Facades\Validator;
use App\trait\Notifications;

class PushNotificationController extends Controller
{
    use Notifications;

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

        $usersQuery = User::where('role', 'user')
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
        });

        if ($request->gender) {
            $usersQuery->where("gender", $request->gender);
        }

        // تعديل منطق الـ Age From (مثال: من سن 20 يعني مواليد 2006 أو أقل)
        if ($request->age_from) {
            $birthYearFrom = Carbon::now()->subYears($request->age_from)->year;
            $usersQuery->whereYear("birthDate", "<=", $birthYearFrom); 
        }

        // تعديل منطق الـ Age To (مثال: إلى سن 30 يعني مواليد 1996 أو أعلى)
        if ($request->age_to) {
            $birthYearTo = Carbon::now()->subYears($request->age_to)->year;
            $usersQuery->whereYear("birthDate", ">=", $birthYearTo);
        } 

        $users = $usersQuery->get();
        foreach ($users as $user) {
            SendPushNotificationJob::dispatch($user, $request->message, $village_id);
        }

        // جلب الـ Tokens وتصفيتها
        $fcm_tokens = $users->pluck("fcm_token")->filter()->toArray();

        // تصحيح المتغيرات المبعوتة (استبدال $data ببيانات الـ $request)
        if (count($fcm_tokens) > 0) {
            // افترضت هنا إن العنوان ثابت أو مبعوت، لو مش مبعوت حط نص من عندك
            $title = "تنبيه جديد"; 
            $this->sendNotificationToMany($fcm_tokens, $title, $request->message);
        }

        return response()->json([
            'message' => 'Notifications queued successfully to ' . $users->count() . ' users.',
        ]);
    }
}
