<?php

namespace App\Http\Controllers\api\User\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\ServiceType;
use App\Models\Provider;
use App\Models\ProviderGallary;
use App\Models\ProviderVideos;
use App\Models\ProviderReview;
use App\Models\Appartment;

class ServiceController extends Controller
{
    public function __construct(private ServiceType $services,
    private Provider $provider, private ProviderGallary $provider_gallery
    , private ProviderVideos $provider_video){}

    public function view(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id' => 'required|exists:villages,id',
            'appartment_id' => 'required|exists:appartments,id', 
            'local' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }

        $appartment = Appartment::
        where('id', $request->appartment_id)
        ->first();
        if(empty($appartment) || !$appartment->options_status || !$appartment->all_status){
            return response()->json([
                'errors' => 'You are blocked to enter this appartment'
            ],400);
        } 
        $services = $this->services
        ->where('status', 1)
        ->whereHas('providers', function($query) use($request){
            $query->where('village_id', $request->village_id);
        })
        ->with(['providers.work_hours']) // load providers with work_hours
        ->get();
        // Optionally filter in PHP
        $services->each(function ($service) use ($request) {
            $service->my_providers = $service->providers
            ->where('status', 1)->where('village_id', $request->village_id)->values()
            ->map(function($item) use($request){
                return [
                    'id' => $item->id,
                    'name' => $request->local == 'en' ?
                    $item->name : $item->ar_name?? $item->name,
                    'image' => $item->image_link,
                    'location' => $item->location,
                    'work_hours' => $item->work_hours,
                    'is_open_now' => $item->isOpenNow(),
                    'status' => $item->status,
                    'service' => $item?->service?->name,
                    'village' => $item?->village?->name,
                    'cover_image' => $item->cover_image_link,
                    'location_map' => $item->location_map,
                    'subscription' => !empty($item->from) && !empty($item->to) 
                    && $item->from <= date('Y-m-d') && $item->to >= date('Y-m-d') ? true : false,

                    'menue' => optional($item?->menue?->where('status', 1))->pluck('image_link') ?? collect([]),
                    'videos' => $item?->videos?->where('status', 1)?->values()->map(function($element){
                        return [
                            'id' => $element->id,
                            'description' => $element->description,
                            'video_link' => $element->video_link,
                            'love_count' => $element->love->count(),
                            'my_love' => $element->my_love->count() > 0 ? true : false,
                        ];
                    }),
                    'watts_status' => $item?->contact?->watts_status ?? 0,
                    'phone_status' => $item?->contact?->phone_status ?? 0,
                    'website_status' => $item?->contact?->website_status ?? 0,
                    'instagram_status' => $item?->contact?->instagram_status ?? 0,
                    'watts' => $item?->contact?->watts ?? null,
                    'phone' => $item?->contact?->phone ?? null,
                    'website' => $item?->contact?->website ?? null,
                    'instagram' => $item?->contact?->instagram ?? null,

                    'zone' => $item?->zone?->translations
                    ->where('locale', $request->local)->first()?->value ?? $item?->zone?->name,
                    'mall' => $item?->mall?->translations
                    ->where('locale', $request->local)->first()?->value ?? $item?->mall?->name,
                    'village' => $item?->village?->name,
                    'gallery' => $item->gallery->map(function($element){
                        return [
                            'id' => $element->id,
                            'image' => $element->image_link,
                            'love_count' => $element->love->count(),
                            'my_love' => $element->my_love->count() > 0 ? true : false,
                        ];
                    }),
                    'description' => $request->local == 'en' ?
                    $item->description : $item->ar_description?? $item->description,
                    'loves_count' => count($item->love_user),
                    'my_love' => count($item->love_user->where('id', $request->user()->id)) > 0
                    ? true :false,
                ];
            });
        });
        $services = $services
        ->map(function($item) use($request){
            return [
                'id' => $item->id,
                'name' => $request->local == 'en' ?
                $item->name : $item->ar_name?? $item->name,
                'image' => $item->image_link,
                'status' => $item->status,
                'description' => $request->local == 'en' ?
                $item->description : $item->ar_description?? $item->description,
                'my_providers' => $item->my_providers, 
            ];
        });

        return response()->json([
            'services' => $services
        ]);
    }

    public function services(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id'    => 'required|exists:villages,id',
            'local'         => 'required|in:en,ar',
            'search'        => 'sometimes|string|nullable',
            'per_page'      => 'sometimes|integer|min:1|max:100',
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 400);
        }

        $search = $request->search;
        $local  = $request->local;

        $services = $this->services
            ->whereHas('providers', function($query) use($request){
                $query->where('village_id', $request->village_id);
            })
            // تحميل علاقة الترجمات مسبقاً لمنع مشكلة الـ N+1 Performance Problem
            ->with('translations') 
            // منطق البحث الذكي المتوافق مع الـ Model الخاص بك
            ->when($request->filled('search'), function($query) use($search, $local) {
                $query->where(function($q) use($search, $local) {
                    // بحث افتراضي في الاسم الإنجليزي المخزن بالجدول الرئيسي
                    $q->where('name', 'like', "%{$search}%");

                    // البحث في الاسم العربي المخزن في جدول الترجمات المورفولوجي
                    $q->orWhereHas('translations', function($transQuery) use($search) {
                        $transQuery->where('key', 'name')
                                ->where('locale', 'ar')
                                ->where('value', 'like', "%{$search}%");
                    });
                });
            })
            ->paginate($request->get('per_page', 15));

        // تشكيل البيانات المرتجعة بناءً على الـ accessors المعرفة في الموديل
        $services->through(function($item) use($local){
            return [
                'id'          => $item->id,
                // استخدام الـ Accessor المسمى ar_name الذي يعود من الموديل تلقائياً
                'name'        => $local == 'en' ? $item->name : ($item->ar_name ?? $item->name),
                // استخدام الـ Accessor المسمى image_link المتوفر بالموديل
                'image'       => $item->image_link, 
                'status'      => $item->status,
                // الموديل الحالي لا يحتوي على حقل وصف (description)، إذا كان موجوداً بقاعدة البيانات قم بإلغاء التعليق عنه أدناه:
                // 'description' => $item->description, 
            ];
        });

        return response()->json([
            'services' => $services
        ]);
    }

    public function services_provider(Request $request){
        $validator = Validator::make($request->all(), [
            'village_id'      => 'sometimes|exists:villages,id',
            'appartment_id'   => 'required|exists:appartments,id', 
            'service_id'      => 'required|exists:service_types,id', 
            'zone_village_id' => 'sometimes|exists:zone_villages,id', 
            'local'           => 'required|in:en,ar',
            'search'          => 'sometimes|string|nullable',
            'per_page'        => 'sometimes|integer|min:1|max:100',
        ]);

        if ($validator->fails()) { 
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ], 400);
        }

        $appartment = Appartment::find($request->appartment_id);

        if (empty($appartment) || !$appartment->options_status || !$appartment->all_status) {
            return response()->json([
                'errors' => 'You are blocked to enter this appartment'
            ], 400);
        } 

        $search = $request->search;
        $local  = $request->local;
        $today  = date('Y-m-d');
        $userId = $request->user()->id;

        $services_providers = Provider::where('status', 1)
            ->where("service_id", $request->service_id)
            // حساب عدد الإعجابات مباشرة من قاعدة البيانات، وفحص إعجاب المستخدم الحالي
            ->withCount([
                'love_user as loves_count',
                'love_user as my_love_count' => fn($q) => $q->where('users.id', $userId),
                ''
            ])
            // جلب العلاقات المطلوبة مسبقاً مع الفلترة والترجمات للأداء العالي
            ->with([
                'work_hours', 'service', 'village', 'contact', 'zone.translations', 'mall.translations',
                'menue' => fn($q) => $q->where('status', 1)
            ])
            // منطق البحث بالاسم (إنجليزي/عربي) أو الهاتف
            ->when($request->filled('search'), function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('ar_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
                });
            });
            if($request->village_id){
                $services_providers->where('village_id', $request->village_id);
            }
            if($request->zone_village_id){
                $services_providers->where('zone_village_id', $request->zone_village_id);
            }

        $services_providers = $services_providers->paginate($request->get('per_page', 15));

        // تشكيل البيانات المرتجعة للـ Pagination
        $services_providers->through(function ($item) use ($request, $local, $today) {
            return [
                'id'               => $item->id,
                'name'             => $local == 'en' ? $item->name : ($item->ar_name ?? $item->name),
                'image'            => $item->image_link,
                'location'         => $item->location,
                'work_hours'       => $item->work_hours,
                'is_open_now'      => $item->isOpenNow(),
                'status'           => $item->status,
                'service'          => $item->service?->name,
                'cover_image'      => $item->cover_image_link,
                'location_map'     => $item->location_map,
                'subscription'     => !empty($item->from) && !empty($item->to) && $item->from <= $today && $item->to >= $today,
                
                'menue'            => $item->menue->pluck('image_link'),
                'watts_status'     => $item->contact?->watts_status ?? 0,
                'phone_status'     => $item->contact?->phone_status ?? 0,
                'website_status'   => $item->contact?->website_status ?? 0,
                'instagram_status' => $item->contact?->instagram_status ?? 0,
                'watts'            => $item->contact?->watts ?? null,
                'phone'            => $item->contact?->phone ?? null,
                'website'          => $item->contact?->website ?? null,
                'instagram'        => $item->contact?->instagram ?? null,

                'zone'             => $item->zone?->translations->where('locale', $local)->first()?->value ?? $item->zone?->name,
                'mall'             => $item->mall?->translations->where('locale', $local)->first()?->value ?? $item->mall?->name,
                'village'          => $item->village?->name,
                'description'      => $local == 'en' ? $item->description : ($item->ar_description ?? $item->description),
                
                'loves_count'      => $item->loves_count,
                'rate'             => $item->rate,
                'my_love'          => $item->my_love_count > 0,
            ];
        }); 

        return response()->json([
            'services_providers' => $services_providers
        ]);
    }
    
    public function services_provider_gallery(Request $request){
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required|exists:appartments,id', 
            'local'       => 'required',
            'per_page'    => 'sometimes|integer|min:1|max:100', // اختياري للتحكم بحجم الصفحة
        ]);
        
        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 400);
        }

        $gallery = ProviderGallary::where('provider_id', $request->provider_id)
            // حساب العدد مباشرة من قاعدة البيانات لتسريع الأداء
            ->withCount(['love', 'my_love']) 
            ->paginate($request->get('per_page', 15)); // افتراضياً 15 عنصر في الصفحة

        // تحويل البيانات للحفاظ على نفس الـ Structure المطلوب مع الـ Pagination
        $gallery->through(function ($item) {
            return [
                'id'         => $item->id,
                'image'      => $item->image_link,
                'love_count' => $item->love_count, // النتيجة تأتي جاهزة من معالج الاستعلام
                'my_love'    => $item->my_love_count > 0,
            ];
        }); 

        return response()->json([
            'gallery' => $gallery
        ]);
    }

    public function services_provider_videos(Request $request){
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required|exists:appartments,id', 
            'local'       => 'required',
            'per_page'    => 'sometimes|integer|min:1|max:100',
        ]);
        
        if ($validator->fails()) { 
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 400);
        }

        $videos = ProviderVideos::where('provider_id', $request->provider_id)
            ->withCount(['love', 'my_love'])
            ->paginate($request->get('per_page', 15));

        $videos->through(function ($item) {
            return [
                'id'          => $item->id,
                'description' => $item->description,
                'video_link'  => $item->video_link,
                'love_count'  => $item->love_count,
                'my_love'     => $item->my_love_count > 0,
            ];
        }); 

        return response()->json([
            'gallery' => $videos // حافظت لك على اسم المفتاح 'gallery' كما أردت في الـ Response
        ]);
    }

    public function out_service(Request $request){
        $validator = Validator::make($request->all(), [
            'local' => 'required',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $services = $this->services
        ->where('status', 1)
        ->whereHas('providers')
        ->with('providers') // load all providers
        ->get();
        
        $services = $services
        ->map(function($item) use($request){
            return [
                'id' => $item->id,
                'name' => $request->local == 'en' ?
                $item->name : $item->ar_name?? $item->name,
                'image' => $item->image_link,
                'status' => $item->status,
                'description' => $request->local == 'en' ?
                $item->description : $item->ar_description?? $item->description,
                'providers' => $item->providers,
            ];
        });

        return response()->json([
            'services' => $services
        ]);
    }

    public function love(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'love' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        
        $love = $request->love;
        $provider = $this->provider
        ->where('id', $id)
        ->first();
        if ($love) {
            $provider->love_user()->detach($request->user()->id);
            $provider->love_user()->attach($request->user()->id);
        } else {
            $provider->love_user()->detach($request->user()->id);
        }
        
        return response()->json([
            'success' => 'You update react success'
        ]);
    }

    public function love_history(Request $request){
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        $providers = $this->provider
        ->whereHas('love_user', function($query) use($request){
            $query->where('users.id', $request->user()->id);
        })
        ->get()
        ->map(function($item) use($request){
            return [
                'id' => $item->id,
                'name' => $request->local == 'en' ?
                $item->name : $item->ar_name?? $item->name,
                'image' => $item->image_link,
                'location' => $item->location,
                'phone' => $item->phone,
                'work_hours' => $item->work_hours,
                'is_open_now' => $item->isOpenNow(),
                'status' => $item->status,
                'description' => $request->local == 'en' ?
                $item->description : $item->ar_description?? $item->description,
                'loves_count' => count($item->love_user),
                'my_love' => count($item->love_user->where('id', $request->user()->id)) > 0
                ? true : false,
                'subscription' => !empty($item->from) && !empty($item->to) 
                && $item->from <= date('Y-m-d') && $item->to >= date('Y-m-d') ? true : false,
            ];
        });
        
        return response()->json([
            'providers' => $providers
        ]);
    }

    public function image_love(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'love' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        
        $love = $request->love;
        $provider_gallery = $this->provider_gallery
        ->where('id', $id)
        ->first();
        if ($love) {
            $provider_gallery->love()->detach($request->user()->id);
            $provider_gallery->love()->attach($request->user()->id);
        } else {
            $provider_gallery->love()->detach($request->user()->id);
        }
        
        return response()->json([
            'success' => 'You update react success'
        ]);
    }

    public function video_love(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'love' => 'required|boolean',
        ]);
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            $firstError = $validator->errors()->first();
            return response()->json([
                'errors' => $firstError,
            ],400);
        }
        
        $love = $request->love;
        $provider_video = $this->provider_video
        ->where('id', $id)
        ->first();
        if ($love) {
            $provider_video->love()->detach($request->user()->id);
            $provider_video->love()->attach($request->user()->id);
        } else {
            $provider_video->love()->detach($request->user()->id);
        }
        
        return response()->json([
            'success' => 'You update react success'
        ]);
    }

    public function check_review(Request $request, $id){
        $check = ProviderReview::where("provider_id", $id)
            ->where("user_id", $request->user()->id)
            ->exists(); 

        return response()->json([
            "check" => $check
        ]);
    }

    public function my_review(Request $request, $id){
        $my_review = ProviderReview::where("provider_id", $id)
            ->where("user_id", $request->user()->id)
            ->first(); 

        return response()->json([
            "my_review" => $my_review
        ]);
    }

    public function update_review(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'rate'        => 'required|numeric|min:1|max:5',
            "comment"     => "sometimes|nullable",
            "provider_id" => "required|exists:providers,id"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 400);
        }

        // التحقق من وجود مراجعة سابقة
        $review = ProviderReview::where("provider_id", $request->provider_id)
            ->where("user_id", $request->user()->id)
            ->first();

        if(!$review){
            return response()->json([
                "errors" => "You must enroll rate",
            ], 400);
        }

        $review->update([
            "rate"        => $request->rate,
            "comment"     => $request->comment,
        ]);

        // جلب بيانات المستخدم المربوطة بالتقييم
        $review->load("user");

        $data = [
            "id"         => $review->id,
            "rate"       => $review->rate,
            "comment"    => $review->comment,
            "user_name"  => $review->user?->name,
            "image_link" => $review->user?->image_link,
            "phone"      => $review->user?->phone,
        ];

        return response()->json([
            "success" => "You add your review success",
            "data"    => $data
        ]);
    }

    public function review(Request $request){
        $validator = Validator::make($request->all(), [
            'rate'        => 'required|numeric|min:1|max:5',
            "comment"     => "sometimes|nullable",
            "provider_id" => "required|exists:providers,id"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 400);
        }

        // التحقق من وجود مراجعة سابقة
        $exists = ProviderReview::where("provider_id", $request->provider_id)
            ->where("user_id", $request->user()->id)
            ->exists();

        if($exists){
            return response()->json([
                "errors" => "You add your review before",
            ], 400);
        }

        $review = ProviderReview::create([
            "rate"        => $request->rate,
            "comment"     => $request->comment,
            "user_id"     => $request->user()->id,
            "provider_id" => $request->provider_id,
        ]);

        // جلب بيانات المستخدم المربوطة بالتقييم
        $review->load("user");

        $data = [
            "id"         => $review->id,
            "rate"       => $review->rate,
            "comment"    => $review->comment,
            "user_name"  => $review->user?->name,
            "image_link" => $review->user?->image_link,
            "phone"      => $review->user?->phone,
        ];

        return response()->json([
            "success" => "You add your review success",
            "data"    => $data
        ]);
    }

    public function show_reviews(Request $request){
        $validator = Validator::make($request->all(), [
            "provider_id" => "required|exists:providers,id"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 400);
        }

        // تم تعديل throught إلى through الصحيحة
        $reviews = ProviderReview::where("provider_id", $request->provider_id)
            ->with("user")
            ->paginate(10)
            ->through(function($item){
                return [
                    "id"         => $item->id,
                    "rate"       => $item->rate,
                    "comment"    => $item->comment,
                    "user_name"  => $item->user?->name,
                    "image_link" => $item->user?->image_link,
                    "phone"      => $item->user?->phone,
                ];
            }); 

        return response()->json([
            "reviews" => $reviews
        ]);
    }
}
