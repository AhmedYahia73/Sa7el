<?php

namespace App\Http\Controllers\api\SuperAdmin\Verification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\VerificationRequest;
use App\Models\User;

class VerificationRequestController extends Controller
{

    public function index(Request $request){
        $validator = Validator::make($request->all(), [
            'search'   => 'nullable|string|max:255',
            'status'   => 'required|in:pending,history',
            'from'     => 'nullable|date',
            'to'       => 'nullable|date',
            'per_page' => 'nullable|integer|min:1|max:100', // للتحكم بعدد العناصر في الصفحة
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        // بناء الاستعلام الأساسي
        $query = VerificationRequest::with("user");

        // فلترة الحالة
        if ($request->status == "pending") {
            $query->where("status", "pending");
        } else {
            $query->where("status", "!=", "pending");
        }

        // فلترة التاريخ من
        if ($request->from) {
            $query->whereDate("created_at", ">=", $request->from);
        }

        // فلترة التاريخ إلى
        if ($request->to) {
            $query->whereDate("created_at", "<=", $request->to);
        }

        // تطبيق البحث في علاقة المستخدم (الاسم، الإيميل، الهاتف)
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // جلب البيانات مجزأة صفحات
        $verification_requests = $query->paginate($perPage);

        // تحويل الهيكل الداخلي للبيانات (Mapping) دون كسر الـ Pagination
        $verification_requests->getCollection()->transform(function($item) {
            return [
                "id"                 => $item->id,
                "user_name"          => $item->user?->name,
                "user_email"         => $item->user?->email,
                "user_phone"         => $item->user?->phone,
                "user_profile_image" => $item->user?->image_link,
                "second_image"       => $item->image_link,
                "status"             => $item->status,
            ];
        });

        return response()->json([
            "verification_requests" => $verification_requests
        ]);
    }

    public function status(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'status'   => 'required|in:approve,reject',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $verification_item = VerificationRequest::findOrFail($id);
        $verification_item->update([
            "status" => $request->status
        ]);
        if($request->status == "approve"){
            User::
            where("id", $verification_item->user_id)
            ->update([
                "verification" => true
            ]);
        }

        return response()->json([
            "success" => "You change status success"
        ]);
    }
}
