<?php

namespace App\Http\Controllers\api\User\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

use App\Models\Application;
use App\Models\HelpVideo;

class ApplicationController extends Controller
{
    public function chat(Request $request) 
    { 
        $validation = Validator::make($request->all(), [  
            'message' => ['required', 'string'],
            'locale' => ['required', "in:en,ar"],
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 422);
        }

        $userQuery = $request->input('message');
        $locale = $request->input('locale');

        $application = Application::first();

        if(!$application){
            return response()->json([
                "errors" => "You should open help"
            ], 400);
        }
        // 1. تعديل وفلترة: إضافة بحث مبدئي وجلب أسماء الأعمدة الصحيحة تماماً من الـ DB
        $records = HelpVideo::
        take(50) // نكتفي بأول 15 ريكورد متطابق عشان سرعة الاستجابة وتوفير التكلفة
        ->get(['id', 'description', 'ar_video', 'en_video']); 

        // 2. تعديل: استخدام أسماء الأعمدة الصحيحة المستخرجة في الخطوة السابقة
        $formattedRecords = $records->map(function ($item) use ($locale) {
        $video = $locale == "en" ? $item->en_video : $item->ar_video;
        return "ID: {$item->id} | Description: {$item->description["en"]} | Video Content Source: {$video}";
        })->implode("\n");

        // 3. تحديد طبيعة عمل التطبيق
        $projectFlow = $application->app_description;

        // 4. تحسين التعليمات: إجبار الـ AI على صياغة خطوات حل المشكلة بالتفصيل
        $systemInstruction = "
        You are the primary technical support assistant for our platform.

        [APPLICATION WORKFLOW CONTEXT]:
        \"{$projectFlow}\"

        [AVAILABLE HELP VIDEOS DATABASE]:
        {$formattedRecords}

        Your Goal:
        1. Carefully analyze the user's incoming problem ('message') and match it against the 'Description' column of the available help videos above.
        2. Select the single absolute best-matching record that resolves their issue.
        3. Formulate a comprehensive, step-by-step guide explaining exactly how the user can resolve their problem. Base your troubleshooting advice on the Application Workflow Context and the chosen video's context.
        4. Write your response entirely in the requested locale language (Language: {$locale}).
        5. Strictly return your output as a single JSON object fitting this schema:
        {
        \"answer\": \"Your step-by-step troubleshooting instructions written out for the user here\",
        \"video\": \"The exact text content/URL from the matching record's video field string\"
        }
        If no database record directly matches the user's specific issue, still provide general helpful step-by-step instructions based on the application context in 'answer' and set 'video' to null.
        ";

        // 5. تجهيز الـ Payload
        $contents = [
        [
            'parts' => [
                ['text' => $userQuery]
            ]
        ]
        ];

        $apiKey = $application->google_api;

        // 6. إرسال الطلب لـ Gemini
        $response = Http::timeout(30)->post(
        "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
        [
            'contents' => $contents,
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3, // درجة حرارة منخفضة لضمان الدقة واختيار الداتا بدون تأليف
                'topK' => 64,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
                'responseMimeType' => 'application/json', // إجبار الموديل على إرجاع JSON نظيف تماماً
            ]
        ]
        );

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $rawJsonText = $data['candidates'][0]['content']['parts'][0]['text'];
                $decodedAiPayload = json_decode(trim($rawJsonText), true);

                // خصم الرصيد من المستخدم بعد نجاح العملية
                $request->user()->requests--;
                $request->user()->save();
                
                return response()->json([
                    'status' => 'success',
                    'answer' => $decodedAiPayload['answer'] ?? 'No precise answer could be formulated.',
                    'video'  => $decodedAiPayload['video'] ?? null
                ]);
            } else {
                return response()->json([
                    'error' => 'No valid response layout returned from Gemini',
                    'data' => $data
                ], 500);
            }
        } else {
            return response()->json([
                'error' => 'API request failed',
                'details' => $response->json()
            ], $response->status());
        }   
    }
}
