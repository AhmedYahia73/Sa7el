<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificationEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    public $notification;

    public function __construct($notification)
    {
        $this->notification = $notification;
        Log::info('🎯 New Notification', ['Notification_id' => $notification->id]);
    } 
 
    public function broadcastOn(): array
    {
        return [
            new Channel('newNotification_' . $this->notification['village_id']),
            new Channel('newNotificationAdmin'),
        ];
    }

    public function broadcastAs(): string
    {
        Log::info('📢 Broadcast As: NewOrderEvent');
        return 'NewOrderEvent';
    }

    public function broadcastWith(): array
    {
        $data = [
            
                'village_id' => $this->notification['village_id'],
                'code_request_id' => $this->notification['code_request_id'],
                'login_request_id' => $this->notification['login_request_id'],
                "type" => $this->notification['type'],
                'notification' => $this->notification['notification'],
        ];
        
        Log::info('📦 Broadcasting Data:', $data);
        
        return $data;
    }
}
