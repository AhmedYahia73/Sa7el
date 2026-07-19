<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
// السطور الأربعة دول كانوا ناقصين عندك ولازم يتضافوا:
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class NotificationChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * القنوات اللي الإشعار هيمشي فيها.
     */
    public function via($notifiable): array
    {
        return [FcmChannel::class, 'broadcast'];
    }

    /**
     * إعداد الإشعار الخاص بـ Firebase (لو التطبيق مقفول)
     */
    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(
            notification: new FcmNotification(
                title: $this->data['title'],
                body: $this->data['body'],
            )
        ))
        ->data([
            'data_id' => (string) $this->data['id'],
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK', 
        ]);
    }

    /**
     * إعداد الإشعار الخاص بـ Reverb (لو التطبيق مفتوح)
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'data_id' => $this->data['id'],
            'title' => $this->data['title'],
            'body' => $this->data['body'], 
        ]);
    }
}