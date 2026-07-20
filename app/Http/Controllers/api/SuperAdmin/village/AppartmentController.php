<?php

namespace App\Http\Controllers\api\SuperAdmin\village;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appartment;
use App\Models\AppartmentCode;
use App\Models\AppartmentType;
use App\Models\Package;
use App\Models\User;
use App\Models\Zone;
use App\Models\Village;
use App\trait\TraitImage; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AppartmentController extends Controller
{
    public function __construct(private Appartment $appartment,
    private AppartmentCode $appartment_code, private Zone $zones,
    private AppartmentType $appartment_type, private User $users){}
    use TraitImage;

    public function view(Request $request, $id){
        $appartments = $this->appartment
        ->where('village_id', $id)
        
        // 1. إضافة شرط البحث الذكي هنا
        ->when($request->filled('search'), function ($query) use ($request) {
            $searchTerm = $request->get('search');
            $query->where('unit', 'LIKE', "%{$searchTerm}%");
        })
        
        ->with([
            'type:id,name,image', 
            'zone:id,name,image,description',
            'appartment_code' => function($query) {
                $query->select(['id', 'code', 'type', 'from', 'to', 'people', 'user_id', 'appartment_id'])
                    ->with('user:id,name');
            }
        ])
        ->paginate($request->get('per_page', 10));

        // 2. تعديل شكل البيانات
        $appartments->through(function($apartment) {
            
            $formatted_codes = $apartment->appartment_code
                ->where("type", "owner")
                ->groupBy('code')
                ->map(function($group, $codeString) {
                    return [
                        'code'   => $codeString,
                        'type'   => $group->first()->type,
                        'from'   => $group->first()->from,
                        'to'     => $group->first()->to,
                        'people' => $group->first()->people,
                        'users'  => $group->map(function($codeItem) {
                            return $codeItem->user;
                        })->filter()->values() 
                    ];
                })->values();
                
            $rent_codes = $apartment->appartment_code
                ->where("type", "renter")
                ->where("from", "<=", date("Y-m-d"))
                ->where("to", ">=", date("Y-m-d"))
                ->groupBy('code')
                ->map(function($group, $codeString) {
                    return [
                        'code'   => $codeString,
                        'type'   => $group->first()->type,
                        'from'   => $group->first()->from,
                        'to'     => $group->first()->to,
                        'people' => $group->first()->people,
                        'users'  => $group->map(function($codeItem) {
                            return $codeItem->user;
                        })->filter()->values() 
                    ];
                })->values();

            // ✨ التعديل هنا: إسناد الكولكشن كاملة مباشرة بدون تحديد Index خاطئ
            $apartment->formatted_codes = $formatted_codes->last();
            $apartment->rent_codes = $rent_codes->last();

            // حذف العلاقة الأصلية نظيفة بعد التعديل
            unset($apartment->appartment_code);

            return $apartment;
        }); 

        return response()->json([ 
            'appartments' => $appartments,  
        ]);
    }

    public function all_units(Request $request){  
        $validator = Validator::make($request->all(), [
            'village_id' => ['sometimes', 'exists:villages,id'],
        ]);
        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $appartments = $this->appartment
        ->when($request->filled('search'), function ($query) use ($request) {
            $searchTerm = $request->get('search');
            $query->where('unit', 'LIKE', "%{$searchTerm}%");
        }) 
        ->with([
            'type:id,name,image', 
            'zone:id,name,image,description',"village",
            'appartment_code' => function($query) {
                $query->select(['id', 'code', 'type', 'from', 'to', 'people', 'user_id', 'appartment_id'])
                    ->with('user:id,name');
            }
        ]);
        if($request->village_id){
            $appartments->where("village_id", $request->village_id);
        }
        $appartments = $appartments
        ->paginate($request->get('per_page', 10));

        // 2. تعديل شكل البيانات
        $appartments->through(function($apartment) {
            
            $formatted_codes = $apartment->appartment_code
                ->where("type", "owner")
                ->groupBy('code')
                ->map(function($group, $codeString) {
                    return [
                        'code'   => $codeString,
                        'type'   => $group->first()->type,
                        'from'   => $group->first()->from,
                        'to'     => $group->first()->to,
                        'people' => $group->first()->people,
                        'users'  => $group->map(function($codeItem) {
                            return $codeItem->user;
                        })->filter()->values() 
                    ];
                })->values();
                
            $rent_codes = $apartment->appartment_code
                ->where("type", "renter")
                ->where("from", "<=", date("Y-m-d"))
                ->where("to", ">=", date("Y-m-d"))
                ->groupBy('code')
                ->map(function($group, $codeString) {
                    return [
                        'code'   => $codeString,
                        'type'   => $group->first()->type,
                        'from'   => $group->first()->from,
                        'to'     => $group->first()->to,
                        'people' => $group->first()->people,
                        'users'  => $group->map(function($codeItem) {
                            return $codeItem->user;
                        })->filter()->values() 
                    ];
                })->values();

            // ✨ التعديل هنا: إسناد الكولكشن كاملة مباشرة بدون تحديد Index خاطئ
            $apartment->formatted_codes = $formatted_codes->last();
            $apartment->rent_codes = $rent_codes->last();

            // حذف العلاقة الأصلية نظيفة بعد التعديل
            unset($apartment->appartment_code);
            $apartment->village_name = $apartment?->village?->name;
            return $apartment;
        }); 

        return response()->json([ 
            'appartments' => $appartments,  
        ]);
    }

    public function village_list(Request $request){
        $villages = Village::
        get()
        ->map(function($apartment) {
            return [
                "id" => $apartment->id,
                "name" => $apartment->name, 
                "zone_id" => $apartment->zone_id, 
            ];
        }); 

        return response()->json([ 
            'villages' => $villages,
        ]);
    }

    public function appartement_list(Request $request){
        $appartments = $this->appartment 
        ->get()
        ->map(function($apartment) {
            return [
                "id" => $apartment->id,
                "unit" => $apartment->unit, 
                "village_id" => $apartment->village_id, 
            ];
        });
        $zones = $this->zones
        ->where('status', 1)
        ->get();
        $appartment_type = $this->appartment_type
        ->where('status', 1)
        ->get();

        return response()->json([ 
            'appartments' => $appartments,
            'zones' => $zones,
            'appartment_type' => $appartment_type,
        ]);
    }

    public function appartement_details(Request $request, $id){
        $appartment = $this->appartment 
        ->where("id", $id)
        ->with("type", "village")
        ->first();
        $appartment = [
            "id" => $appartment->id,
            "unit" => $appartment->unit, 
            "location" => $appartment->location, 
            "type" => $appartment?->type?->name, 
            "village" => $appartment?->village?->name, 
            "created_at" => $appartment->created_at, 
        ];

        return response()->json([ 
            'appartment' => $appartment, 
        ]);
    }
 

    public function user_list(Request $request){ 
        $validator = Validator::make($request->all(), [
            'search' => ['sometimes'],
        ]);
        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $search = request('search');

        $users = $this->users
            ->where('role', 'user')
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%')
                ->where('phone', 'like', '%' . $search . '%');
            })
            ->paginate(10)
            ->through(function($item) {
                return [
                    "id" => $item->id,
                    "name" => $item->name,
                    "phone" => $item->phone,
                ];
            });

        return response()->json([ 
            'users' => $users,
        ]);
    }

    public function view_codes(Request $request, $id){
        $appartment_codes = AppartmentCode::where('appartment_id', $id)
            ->select(['id', 'code', 'type', 'from', 'to', 'people']) // تحديد الأعمدة المطلوبة من الداتابيز مباشرة
            ->get()
            ->unique('code') // فلترة الأكواد المكررة في الـ Collection
            ->values(); // إعادة ترتيب الـ Keys عشان يرجع كـ Array سليم في الـ JSON

        return response()->json([
            'appartment_codes' => $appartment_codes,
        ]);
    }

    public function delete_user_appartment(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => ['required', 'exists:appartments,id'],
            'user_id' => ['required', 'exists:users,id'],
        ]);
        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        AppartmentCode::
        where("user_id", $request->user_id)
        ->where("appartment_id", $request->appartment_id)
        ->where("type", "owner")
        ->delete();

        return response()->json([
            "success" => "You delete data success"
        ]);
    }

    public function create_code(Request $request){
        if ($request->type == 'owner') {
            $validator = Validator::make($request->all(), [
                'appartment_id' => ['required', 'exists:appartments,id'],
                'type' => ['required', 'in:owner,renter'], 
                'people' => ['required', 'numeric'],
            ]);
        } 
        else {
            $validator = Validator::make($request->all(), [
                'appartment_id' => ['required', 'exists:appartments,id'],
                'type' => ['required', 'in:owner,renter'],
                'from' => ['required', 'date_format:Y-m-d H:i:s'],
                'to' => ['required', 'date_format:Y-m-d H:i:s'],
                'people' => ['required', 'numeric'],
                'image' => ['required', 'array'], 
            ]);
        }
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }

        $appartment_code = $this->appartment_code
        ->where('appartment_id', $request->appartment_id)
        ->where('type', 'owner')
        ->whereNotNull('user_id')
        ->first();
        if (!empty($appartment_code)) {
            return response()->json([
                'errors' => "This appartment has owner you can't buy it"
            ], 400);
        }
        $codeRequest = $validator->validated();
        do {
            $code = mt_rand(1000000, 9999999); // Always 7 digits
        } while ($this->appartment_code::where('code', $code)->exists());
        $village_id = Appartment::
        where("id", $request->appartment_id)
        ->first();
        $codeRequest['code'] = $code;
        $codeRequest['village_id'] = $village_id;
        if ($request->has('image')) {
            $image_path = [];
            foreach ($request->image as $item) {
                $image_path[] = $this->uploadFile($item, '/village/appartment_code/id');
            }
            $codeRequest['image'] = $image_path;
        }
        for ($i = 0; $i < $request->people; $i++) {
            $this->appartment_code
            ->create($codeRequest);
        }

        return response()->json([
            'success' => $code
        ]);
    }
    
    public function update_code(Request $request, $id)
    { 
        // 1. الـ Validation
        $validator = Validator::make($request->all(), [ 
            'people' => ['required', 'integer', 'min:1'],
            'from' => ["sometimes", "date_format:Y-m-d H:i:s"],
            'to' => ["sometimes", "date_format:Y-m-d H:i:s"],
        ]); 

        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        // 2. استخدام Transaction لحماية البيانات
        DB::beginTransaction();
        try {
            $appartment_code = $this->appartment_code->findOrFail($id);
            $codes = $this->appartment_code
                ->where("code", $appartment_code->code)
                ->get();

            // تحديد الحقول المطلوبة بالترتيب لضمان تطابق مصفوفة الـ Bulk Insert
            $fillableFields = [
                'appartment_id', 'user_id', 'village_id', 'from', 'to', 
                'type', 'code', 'image', 'owner_id', 'user_type', 
                'people', 'created_at', 'updated_at'
            ];

            // تجهيز التواريخ الجديدة لو مبعوتة
            $fromDate = $request->from ?? $appartment_code->from;
            $toDate = $request->to ?? $appartment_code->to;

            // تجهيز حقل الصورة الافتراضي وتحويله إلى صيغة JSON متوافقة مع قاعدة البيانات
            $defaultImage = null;
            if (!empty($appartment_code->image)) {
                // نقوم بعمل json_encode لإجبار القيمة على مطابقة الـ JSON constraint في الـ DB
                // إذا كانت الصورة مخزنة مسبقاً كـ JSON صالح، نمررها كما هي، وإلا نحولها.
                $defaultImage = $this->isJson($appartment_code->image) 
                    ? $appartment_code->image 
                    : json_encode($appartment_code->image);
            }

            // تجهيز البيانات الافتراضية للسجلات الجديدة (الـ else)
            $baseData = [
                'appartment_id' => $appartment_code->appartment_id,
                'user_id'       => null,
                'village_id'    => $appartment_code->village_id,
                'from'          => $fromDate,
                'to'            => $toDate,
                'type'          => $appartment_code->type,
                'code'          => $appartment_code->code,
                'image'         => $defaultImage,
                'owner_id'      => $appartment_code->owner_id,
                'user_type'     => $appartment_code->user_type,
                'people'        => $request->people,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];

            // 3. مسح البيانات القديمة
            $this->appartment_code->where("code", $appartment_code->code)->delete();  

            // 4. بناء مصفوفة السجلات مع إجبار الترتيب والتطابق
            $records = [];
            for ($i = 0; $i < $request->people; $i++) {
                if (isset($codes[$i])) {
                    // تحديد الصورة الحالية وتحويلها لـ JSON إذا لم تكن كذلك
                    $currentImage = null;
                    if (!empty($codes[$i]->image)) {
                        $currentImage = $this->isJson($codes[$i]->image) 
                            ? $codes[$i]->image 
                            : json_encode($codes[$i]->image);
                    }

                    // السجل موجود مسبقاً: نحدث البيانات ونحافظ على الـ user_id والـ created_at القديم
                    $record = [
                        'appartment_id' => $codes[$i]->appartment_id,
                        'user_id'       => $codes[$i]->user_id,
                        'village_id'    => $codes[$i]->village_id,
                        'from'          => $request->from ?? $codes[$i]->from,
                        'to'            => $request->to ?? $codes[$i]->to,
                        'type'          => $codes[$i]->type,
                        'code'          => $codes[$i]->code,
                        'image'         => $currentImage,
                        'owner_id'      => $codes[$i]->owner_id,
                        'user_type'     => $codes[$i]->user_type,
                        'people'        => $request->people,
                        'created_at'    => $codes[$i]->created_at ?? now(),
                        'updated_at'    => now(),
                    ];
                } else {
                    // سجل جديد زائد عن العدد القديم
                    $record = $baseData;
                }

                // السحر هنا: إعادة فرز المصفوفة لتطابق ترتيب الحقول اللي حددناها فوق بالظبط
                $sortedRecord = [];
                foreach ($fillableFields as $field) {
                    $sortedRecord[$field] = $record[$field];
                }

                $records[] = $sortedRecord;
            }

            // تنفيذ الإدخال السريع دفعة واحدة
            $this->appartment_code->insert($records);

            // تأكيد الحفظ
            DB::commit();

            return response()->json([
                'success' => "Data updated successfully"
            ]);

        } catch (\Exception $e) {
            // التراجع في حالة الخطأ
            DB::rollBack();
            
            return response()->json([
                'error' => "Something went wrong, please try again.",
                'debug' => $e->getMessage() 
            ], 500);
        }
    }

    /**
     * دالة مساعدة للتأكد مما إذا كانت القيمة مسبقاً بصيغة JSON صالحة
     */
    private function isJson($string) {
        if (!is_string($string)) {
            return false;
        }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'unit' => ['required'], 
            'village_id' => ['required', "exists:villages,id"], 
            'appartment_type_id' => ['required', 'exists:appartment_types,id'],
            'location' => ['sometimes'],
            'entrance_status' => ['required', "boolean"],
            'pool_status' => ['required', "boolean"],
            'beach_status' => ['required', "boolean"],
            'rent_code_status' => ['required', "boolean"],
            'selling_status' => ['required', "boolean"],
            'rent_status' => ['required', "boolean"],
            'visits_status' => ['required', "boolean"],
            'options_status' => ['required', "boolean"],
            'all_status' => ['required', "boolean"],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        //units_num
            // 'image' => ['required'],
            // 'village_id' => ['required'],
        $appartmentRequest = $validator->validated();
        // if ($request->has('image')) {
        //     $image_path = $this->upload($request, 'image', '/village/appartment');
        //     $appartmentRequest['image'] = $image_path;
        // }
        $this->appartment
        ->create($appartmentRequest);

        return response()->json([
            'success' => 'You add data success',
        ]);
    }
    
    public function modify(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'unit' => ['required'], 
            'appartment_type_id' => ['required', 'exists:appartment_types,id'],
            'location' => ['sometimes'],
            'village_id' => ['required', "exists:villages,id"], 
            'entrance_status' => ['required', "boolean"],
            'pool_status' => ['required', "boolean"],
            'beach_status' => ['required', "boolean"],
            'rent_code_status' => ['required', "boolean"],
            'selling_status' => ['required', "boolean"],
            'rent_status' => ['required', "boolean"],
            'visits_status' => ['required', "boolean"],
            'options_status' => ['required', "boolean"],
            'all_status' => ['required', "boolean"],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $appartment = $this->appartment
        ->where('id', $id) 
        ->first();
        $appartmentRequest = $validator->validated();
        // if ($request->has('image')) {
        //     $image_path = $this->update_image($request, $appartment->image, 'image', '/village/appartment');
        //     $appartmentRequest['image'] = $image_path;
        // }
        $appartment->update($appartmentRequest);

        return response()->json([
            'success' => 'You update data success',
        ]);
    }
    
    public function delete($id){
        $appartment = $this->appartment
        ->where('id', $id) 
        ->first();
        if (empty($appartment)) {
            return response()->json([
                'errors' => 'Admin not found'
            ], 400);
        }
        $this->deleteImage($appartment->image);
        $appartment->delete();

        return response()->json([
            'success' => 'You delete data success',
        ]);
    }

    public function unit_renters(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id',
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $rents = AppartmentCode::
            with('owner:id,name,phone', 'user:id,name,phone')
            ->where('type', 'renter')  
            ->where("appartment_id", $request->appartment_id)
            ->where("to", ">=", date("Y-m-d"))
            ->orderByDesc('id')
            ->get(); 

        return response()->json([
            'rents' => $rents,
            'rents_count' => $rents->count(),
        ]);
    }

    public function unit_owners(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id',
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $owner = AppartmentCode::
            with('user:id,name,phone')
            ->where('type', 'owner')  
            ->where("appartment_id", $request->appartment_id)
            ->get(); 

        return response()->json([
            'owner' => $owner,
            'owner_count' => $owner->count(),
        ]);
    }

    public function unit_report(Request $request){
        $validator = Validator::make($request->all(), [
            'appartment_id' => 'required|exists:appartments,id',
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $owner = AppartmentCode::
        where('type', 'owner')  
        ->where("appartment_id", $request->appartment_id)
        ->count(); 

        $rents = AppartmentCode::
        where('type', 'renter')  
        ->where("appartment_id", $request->appartment_id)
        ->where("to", ">=", date("Y-m-d"))
        ->count(); 

        return response()->json([
            'owner' => $owner, 
            'rents' => $rents, 
        ]);
    }
}
