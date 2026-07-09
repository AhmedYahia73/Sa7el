<?php

namespace App\Http\Controllers\api\user\VerificationImage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use App\Models\Application;

class VerificationImageController extends Controller
{
    public function verifyFaces(Request $request)
    {
        // 1. التحقق من وجود النصوص في الـ Request
        $request->validate([
            'face_one_base64' => 'required|string',
            'face_two_base64' => 'required|string',
        ]);

        // 2. تنظيف الـ Base64 من الديباجة الـ (Data URI Prefix) لو موجودة
        // مثلاً لو الفرونت بعت: data:image/jpeg;base64,/9j/4AAQSkZ...
        $faceOneCleaned = $this->cleanBase64($request->face_one_base64);
        $faceTwoCleaned = $this->cleanBase64($request->face_two_base64);

        // تخمين الـ Mime Type (افتراضياً jpeg أو png بناءً على الديباجة أو نثبتها jpeg)
        $mimeTypeOne = $this->getMimeType($request->face_one_base64);
        $mimeTypeTwo = $this->getMimeType($request->face_two_base64);

        // 3. تحضير الـ Prompt الصارم لإجبار الموديل على الرد بـ JSON فقط
        $prompt = "Compare these two images. Is the person in the first image the exact same person in the second image? " . 
                  "Answer strictly in JSON format with two keys: 'verified' (boolean: true or false) and 'confidence_percentage' (integer from 0 to 100). " . 
                  "Do not include any markdown formatting like ```json or any extra text outside the JSON object.";

        $GEMINI_API_KEY = Application::first()?->google_api;
        try {
            // 4. إرسال الطلب لـ Gemini API (موديل gemini-2.5-flash الأحدث والأسرع)
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post("[https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=](https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=)" . $GEMINI_API_KEY, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeTypeOne,
                                    'data' => $faceOneCleaned
                                ]
                            ],
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeTypeTwo,
                                    'data' => $faceTwoCleaned
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            // 5. معالجة الرد
            if ($response->failed()) {
                return response()->json([
                    'status' => false,
                    'message' => 'فشل الاتصال بخدمة التحقق من الوجوه.',
                    'error' => $response->body()
                ], 500);
            }

            $responseData = $response->json();
            
            // استخراج النص الراجع من الـ API
            $aiTextResponse = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$aiTextResponse) {
                return response()->json(['status' => false, 'message' => 'يرجى اعطاء صورة اوضح.'], 500);
            }

            // تحويل الـ JSON النصي الراجع من الذكاء الاصطناعي إلى Array في PHP
            $result = json_decode(trim($aiTextResponse), true);

            // للتأكد إن الـ JSON الراجع سليم ومفيهوش مشاكل في الـ Parsing
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'status' => false, 
                    'message' => 'فشل في تحليل البيانات المستلمة.',
                    'raw_response' => $aiTextResponse
                ], 500);
            }

            // 6. الرد النهائي على الـ Frontend
            return response()->json([
                'success' => true,
                'is_identical' => $result['verified'], // true أو false
                'confidence' => $result['confidence_percentage'] . '%', // نسبة التأكيد
                'message' => $result['verified'] ? 'تم التحقق بنجاح، الشخص متطابق.' : 'الصور لا تنتمي لنفس الشخص.'
            ]);

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
 
    private function getMimeType($base64String)
    {
        if (Str::contains($base64String, 'data:image/')) {
            preg_match('/data:([^;]+);/', $base64String, $matches);
            return $matches[1] ?? 'image/jpeg';
        }
        return 'image/jpeg'; // افتراضي في حال لم ترسل الديباجة
    }
}
