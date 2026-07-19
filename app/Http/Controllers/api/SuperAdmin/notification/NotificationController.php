<?php

namespace App\Http\Controllers\api\SuperAdmin\notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Village;
use App\Jobs\SendPushNotificationJob;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\trait\Notifications;

class NotificationController extends Controller
{
    use Notifications;

    public function lists(){
        $villages = Village::
        where("status", true)
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "name" => $item->name,
            ];
        });

        return response()->json([
            "villages" => $villages
        ]);
    }

    public function push_notification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message'    => 'required|string',
            'village_id' => 'sometimes|nullable|exists:villages,id',
            'age_from'   => "numeric|sometimes|nullable",
            'age_to'     => "numeric|sometimes|nullable", 
            'gender'     => "sometimes|in:male,female", 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $usersQuery = User::where('role', 'user');

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

        if ($request->village_id) {
            $usersQuery->whereHas('appartment_code', function ($query) use ($request) {
                // حصر الـ OR جوة قوسين عشان حماية الـ Query
                $query->where(function ($q) use ($request) {
                    $q->where(function ($sub) use ($request) {
                        $sub->where('type', 'owner')
                            ->where('village_id', $request->village_id);
                    })->orWhere(function ($sub) use ($request) {
                        $sub->where('type', 'renter')
                            ->where('village_id', $request->village_id)
                            ->whereDate('from', '<=', now())
                            ->whereDate('to', '>=', now());
                    });
                });
            });
        }

        $users = $usersQuery->get();

        foreach ($users as $user) {
            SendPushNotificationJob::dispatch($user, $request->message, $request->village_id);
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
