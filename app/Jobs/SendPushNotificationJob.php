<?php

namespace App\Jobs;

use App\Events\UserNotification;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\NotificationChanged;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private User $user,
        private string $message,
        private ?int $village_id = null,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [
            'village_id'       => $this->village_id,
            'code_request_id'  => null,
            'login_request_id' => null,
            'type'             => 'user',
            'notification'     => $this->message,
            'is_read'          => 0,
            'user_id'          => $this->user->id,
        ];

        // بث الحدث عبر Reverb
        UserNotification::dispatch($data);

        // حفظ الإشعار في قاعدة البيانات
        $new_notification = Notification::create($data);

        // إرسال إشعار Firebase
        $notificationData = [
            'id'    => $new_notification->id,
            'title' => 'تنبيه هام',
            'body'  => $this->message,
        ];

        $this->user->notify(new NotificationChanged($notificationData));
    }
}
