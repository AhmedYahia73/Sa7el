<?php

namespace App\Http\Controllers\api\user\VerificationImage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Aws\Rekognition\RekognitionClient;

class VerificationImageController extends Controller
{
    public function verifyFaces(Request $request)
    {
        // 1. Validation
        $request->validate([
            'face_one_base64' => 'required|string',
            'face_two_base64' => 'required|string',
        ]);

        // 2. تنظيف الـ Base64 (شيل الديباجة عشان نحولها لـ Binary صلب)
        $faceOneCleaned = $this->cleanBase64($request->face_one_base64);
        $faceTwoCleaned = $this->cleanBase64($request->face_two_base64);

        // 3. تحويل الـ Base64 إلى داتا باينري (Decoded Bytes) لأن AWS بتحتاجها كدة
        $imageSourceBytes = base64_decode($faceOneCleaned);
        $imageTargetBytes = base64_decode($faceTwoCleaned);

        if (!$imageSourceBytes || !$imageTargetBytes) {
            return response()->json(['status' => false, 'message' => 'نص الـ Base64 المرسل غير صالح.'], 400);
        }

        try {
            // 4. تهيئة عميل AWS باستخدام البيانات اللي حطيناها في الـ config
            $rekognition = new RekognitionClient([
                'version'     => 'latest',
                'region'      => config('services.aws.region'),
                'credentials' => [
                    'key'    => config('services.aws.key'),
                    'secret' => config('services.aws.secret'),
                ],
            ]);

            // 5. إرسال الطلب للمقارنة
            $result = $rekognition->compareFaces([
                'SimilarityThreshold' => 80.0, // نسبة الحزم (80% فما فوق يعتبر نفس الشخص)
                'SourceImage' => [
                    'Bytes' => $imageSourceBytes, // تمرير الباينري مباشرة
                ],
                'TargetImage' => [
                    'Bytes' => $imageTargetBytes,
                ],
            ]);

            // 6. قراءة وفك تحليل النتيجة
            $faceMatches = $result['FaceMatches'] ?? [];

            if (count($faceMatches) > 0) {
                // لقى تطابق! وبياخد أول وش وأعلى نسبة تشابه
                $similarity = $faceMatches[0]['Similarity'];

                return response()->json([
                    'success' => true,
                    'is_identical' => true,
                    'confidence' => round($similarity, 2) . '%',
                    'message' => 'تم التحقق بنجاح، الشخص متطابق.'
                ]);
            }

            // لو ملقاش تطابق في مصفوفة الـ Matches
            return response()->json([
                'success' => true,
                'is_identical' => false,
                'confidence' => '0%',
                'message' => 'الصور لا تنتمي لنفس الشخص أو الوجوه غير واضحة.'
            ]);

        } catch (\Aws\Exception\AwsException $e) {
            // لقط أي إيرور خاص بـ AWS (مثل: مشكلة في الـ Credentials أو الـ Region)
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ في خدمة AWS الخدمية.',
                'error' => $e->getAwsErrorMessage() ?? $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ غير متوقع أثناء المعالجة.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function cleanBase64($base64String)
    {
        if (Str::contains($base64String, ';base64,')) {
            return explode(';base64,', $base64String)[1];
        }
        return $base64String;
    }
}
// namespace App\Http\Controllers\api\user\VerificationImage;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Str;

// use App\Models\Application;

// class VerificationImageController extends Controller
// {
//     public function verifyFaces(Request $request)
//     {
//         // 1. التحقق من وجود النصوص في الـ Request
//         $request->validate([
//             'face_one_base64' => 'required|string',
//             'face_two_base64' => 'required|string',
//         ]);

//         // 2. تنظيف الـ Base64 من الديباجة الـ (Data URI Prefix) لو موجودة
//         // مثلاً لو الفرونت بعت: data:image/jpeg;base64,/9j/4AAQSkZ...
//         $faceOneCleaned = $this->cleanBase64($request->face_one_base64);
//         $faceTwoCleaned = $this->cleanBase64($request->face_two_base64);

//         // تخمين الـ Mime Type (افتراضياً jpeg أو png بناءً على الديباجة أو نثبتها jpeg)
//         $mimeTypeOne = $this->getMimeType($request->face_one_base64);
//         $mimeTypeTwo = $this->getMimeType($request->face_two_base64);

//         // 3. تحضير الـ Prompt الصارم لإجبار الموديل على الرد بـ JSON فقط
//         $prompt = "Compare these two images. Is the person in the first image the exact same person in the second image? " . 
//                   "Answer strictly in JSON format with two keys: 'verified' (boolean: true or false) and 'confidence_percentage' (integer from 0 to 100). " . 
//                   "Do not include any markdown formatting like ```json or any extra text outside the JSON object.";
//         $GEMINI_API_KEY = Application::first()?->google_api;

//         // شرط أمان للتأكد إن المفتاح موجود في الداتابيز فعلاً وميجيبش null
//         if (!$GEMINI_API_KEY) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'لم يتم العثور على مفتاح Google API في إعدادات التطبيق.'
//             ], 500);
//         }
        
//         try {
//             // 4. الرابط الصافي بدون أي أقواس أو ديباجة الـ Markdown
//             $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $GEMINI_API_KEY;

//             $response = Http::timeout(30)->withHeaders([
//                 'Content-Type' => 'application/json'
//             ])->post($apiUrl, [
//                 'contents' => [
//                     [
//                         'parts' => [
//                             ['text' => $prompt],
//                             [
//                                 'inlineData' => [
//                                     'mimeType' => $mimeTypeOne,
//                                     'data' => $faceOneCleaned
//                                 ]
//                             ],
//                             [
//                                 'inlineData' => [
//                                     'mimeType' => $mimeTypeTwo,
//                                     'data' => $faceTwoCleaned
//                                 ]
//                             ]
//                         ]
//                     ]
//                 ]
//             ]);

//             // 5. معالجة الرد
//             if ($response->failed()) {
//                 return response()->json([
//                     'status' => false,
//                     'message' => 'فشل الاتصال بخدمة التحقق من الوجوه.',
//                     'error' => $response->body()
//                 ], 500);
//             }

//             $responseData = $response->json();
            
//             // استخراج النص الراجع من الـ API
//             $aiTextResponse = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

//             if (!$aiTextResponse) {
//                 return response()->json(['status' => false, 'message' => 'يرجى اعطاء صورة اوضح.'], 500);
//             }

//             // تحويل الـ JSON النصي الراجع من الذكاء الاصطناعي إلى Array في PHP
//             $result = json_decode(trim($aiTextResponse), true);

//             // للتأكد إن الـ JSON الراجع سليم ومفيهوش مشاكل في الـ Parsing
//             if (json_last_error() !== JSON_ERROR_NONE) {
//                 return response()->json([
//                     'status' => false, 
//                     'message' => 'فشل في تحليل البيانات المستلمة.',
//                     'raw_response' => $aiTextResponse
//                 ], 500);
//             }

//             // 6. الرد النهائي على الـ Frontend
//             return response()->json([
//                 'success' => true,
//                 'is_identical' => $result['verified'], // true أو false
//                 'confidence' => $result['confidence_percentage'] . '%', // نسبة التأكيد
//                 'message' => $result['verified'] ? 'تم التحقق بنجاح، الشخص متطابق.' : 'الصور لا تنتمي لنفس الشخص.'
//             ]);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'حدث خطأ غير متوقع أثناء المعالجة.',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }
 
//     private function cleanBase64($base64String)
//     {
//         if (Str::contains($base64String, ';base64,')) {
//             return explode(';base64,', $base64String)[1];
//         }
//         return $base64String;
//     }
 
//     private function getMimeType($base64String)
//     {
//         if (Str::contains($base64String, 'data:image/')) {
//             preg_match('/data:([^;]+);/', $base64String, $matches);
//             return $matches[1] ?? 'image/jpeg';
//         }
//         return 'image/jpeg'; // افتراضي في حال لم ترسل الديباجة
//     }
// }
