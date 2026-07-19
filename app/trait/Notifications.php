<?php

namespace App\trait;

use Illuminate\Http\Request;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\ApnsConfig;
trait Notifications
{
    protected $messaging;

    public function sendNotificationToMany(
        array $tokens,
        string $title,
        string $body,
        array $data = []
    ): ?MulticastSendReport {
        
        // 1. تنظيف الـ Tokens من القيم الفاضية
        $tokens = array_filter($tokens); 

        if (count($tokens) > 0) {
            // 2. تهيئة الفايربيز مرة واحدة خارج اللوب
            $factory = (new Factory)->withServiceAccount(config('services.firebase.credentials'));
            $this->messaging = $factory->createMessaging();

            // 3. تجهيز رسالة الإشعار وثابتة لكل الدفعات
            $message = CloudMessage::new()
                ->withNotification(Notification::create($title, $body))
                ->withData($data)
                ->withApnsConfig(
                    ApnsConfig::fromArray([
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                            ],
                        ],
                    ])
                );

            // 4. تقسيم الـ Tokens إلى مجموعات (كل مجموعة 500 كحد أقصى) لتجنب حدود الفايربيز
            $tokenChunks = array_chunk($tokens, 500);
            
            // متغير لتجميع التقارير وإرجاعها في النهاية
            $finalReport = null;

            foreach ($tokenChunks as $chunk) {
                $report = $this->messaging->sendMulticast($message, $chunk);
                
                // دمج التقارير لو عدد الـ Tokens أكبر من 500
                if ($finalReport === null) {
                    $finalReport = $report;
                } else {
                    $finalReport = $finalReport->merge($report);
                }
            }

            return $finalReport;
        }

        return null; 
    }
}