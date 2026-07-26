<?php

namespace App\Http\Controllers\api\Village\Appartments;

use App\Http\Controllers\Controller;
use App\Models\Appartment;
use App\Models\AppartmentCode;
use App\Models\AppartmentType;
use App\Models\Package;
use App\Models\User;
use App\Models\Zone;
use App\trait\TraitImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AppartmentController extends Controller
{
    public function __construct(private Appartment $appartment,
    private AppartmentCode $appartment_code, private Zone $zones,
    private AppartmentType $appartment_type, private User $users){}
    use TraitImage;

    public function view(Request $request){
        $appartments = $this->appartment
        ->where('village_id', $request->user()->village_id)
        
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
        $units = $this->appartment
        ->where('village_id', $request->user()->village_id)
        ->whereDoesntHave('appartment_code')
        ->with('type:id,name,image', 'zone:id,name,image,description')
        ->get();
        $zones = $this->zones
        ->where('status', 1)
        ->get();
        $appartment_type = $this->appartment_type
        ->where('status', 1)
        ->get();
        $users = $this->users
        ->where('role', 'user')
        ->get();

        return response()->json([ 
            'appartments' => $appartments, 
            'units' => $units, 
            'zones' => $zones, 
            'appartment_type' => $appartment_type, 
            'users' => $users, 
        ]);
    }
    
// 1176>appartment>789
    public function appartement_list(Request $request){
        $appartments = $this->appartment
        ->where('village_id', $request->user()->village_id) 
        ->get()
        ->map(function($apartment) {
            return [
                "id" => $apartment->id,
                "unit" => $apartment->unit, 
            ];
        });

        return response()->json([ 
            'appartments' => $appartments,
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
                'image' => ['required',  'array'], 
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
        $codeRequest = $validator->validated();
        do {
            $code = mt_rand(1000000, 9999999); // Always 7 digits
        } while ($this->appartment_code::where('code', $code)->exists());
        $codeRequest['code'] = $code;
        $codeRequest['village_id'] = $request->user()->village_id;
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
        // 1. تحسين الـ Validation
        $validator = Validator::make($request->all(), [ 
            'people' => ['required', 'integer', 'min:1'],
            'from' =>["sometimes", "date_format:Y-m-d H:i:s"],
            'to' =>["sometimes", "date_format:Y-m-d H:i:s"],
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

            // تجهيز البيانات الأساسية (تم إضافة user_id هنا لتجنب نقص الحقول)
            $data = $appartment_code->only([
                'appartment_id', 'village_id', 'from', 
                'to', 'type', 'code', 'image', 'owner_id', 'user_type'
            ]);
            // image column is json — ensure it's encoded as string for raw insert
            if (isset($data['image']) && is_array($data['image'])) {
                $data['image'] = json_encode($data['image']);
            }
            
            // إصلاح المشكلة: تحديث عدد الأشخاص بالرقم الجديد من الـ Request
            $data['people']     = $request->people;
            $data['created_at'] = now();
            $data['updated_at'] = now();
            $data['user_id'] = null;
            if($request->from){
                $data['from'] = $request->from;
            }
            if($request->to){
                $data['to'] = $request->to;
            }

            // 3. مسح البيانات القديمة
            $this->appartment_code->where("code", $appartment_code->code)->delete();  

            // 4. تجهيز مصفوفة لعمل إدخال مرة واحدة (Bulk Insert) بتطابق كامل للحقول
            $records = [];
            for ($i = 0; $i < $request->people; $i++) {
                if (isset($codes[$i])) {
                    // نأخذ البيانات القديمة مع الحفاظ على نفس ترتيب الحقول والـ Timestamps
                    $record = $codes[$i]->only([
                        'appartment_id', 'user_id', 'village_id', 'from', 
                        'to', 'type', 'code', 'image', 'owner_id', 'user_type'
                    ]);
                    if($request->from){
                        $record['from'] = $request->from;
                    }
                    if($request->to){
                        $record['to'] = $request->to;
                    }
                    $record['people']     = $request->people;
                    $record['created_at'] = $codes[$i]->created_at ?? now(); // الحفاظ على وقت الإنشاء القديم أو الحالي
                    $record['updated_at'] = now();
                } else {
                    // لو السجلات الجديدة أكبر من القديمة، نستخدم مصفوفة $data المتطابقة تماماً في الحقول
                    $record = $data;
                }
                // image column is json — ensure it's encoded as string for raw insert
                if (isset($record['image']) && is_array($record['image'])) {
                    $record['image'] = json_encode($record['image']);
                }
                $records[] = $record;
            }

            // تنفيذ الإدخال كاستعلام واحد سريع في الداتابيز
            $this->appartment_code->insert($records);

            // حفظ التغييرات في الداتابيز
            DB::commit();

            return response()->json([
                'success' => "Data updated successfully"
            ]);

        } catch (\Exception $e) {
            // التراجع عن أي تغييرات لو حصل خطأ
            DB::rollBack();
            
            // نصيحة: أثناء التطوير يمكنك استرجاع $e->getMessage() لمعرفة تفاصيل أي خطأ آخر قد يظهر
            return response()->json([
                'error' => "Something went wrong, please try again.",
                'debug' => $e->getMessage() // احذف هذا السطر في مرحلة الـ Production
            ], 500);
        }
    } 

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'unit' => ['required'], 
            'appartment_type_id' => ['required', 'exists:appartment_types,id'],
            'location' => ['sometimes'],
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }
        $package_id = $request->user()->village->package_id;
        $package = Package::
        where("id", $package_id)
        ->first();
        $appartments = $this->appartment
        ->where('village_id', $request->user()->village_id)
        ->count();
        if(!$package || $package->units_num < $appartments + 1){
            return response()->json([
                "errors" => "You must upgrade your plan"
            ], 400);
        }
        //units_num
            // 'image' => ['required'],
            // 'village_id' => ['required'],
        $appartmentRequest = $validator->validated();
        $appartmentRequest['village_id'] = $request->user()->village_id;
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
        if(!$request->user()->delete_unit_role){
            return response()->json([
                'errors' => 'You dont have premission'
            ], 400);
        }
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