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

class UserNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    public $notification;

    public function __construct($notification)
    {
        $this->notification = $notification;
        Log::info('🎯 New Notification', ['village_id' => $notification['village_id']]);
    } 
 
    public function broadcastOn(): array
    {
        return [
            new Channel('userNotification_' . $this->notification['user_id']), 
            new Channel('userNotification_'), 
        ];
    }

    public function broadcastAs(): string
    {
        Log::info('📢 Broadcast As: NewNotificationEvent');
        return 'UserNotificationEvent';
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