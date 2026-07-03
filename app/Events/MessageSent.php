<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * استقبال كائن الرسالة عند إنشائها
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * تحديد القناة (Channel) التي سيتم البث عبرها.
     * سنستخدم قناة خاصة (Private) لضمان الأمان وعدم تجسس مستخدم آخر على المحادثة.
     */
    public function broadcastOn(): array
    {
        // القناة ستكون باسم المحادثة الفريدة، مثلاً: chat.5
        return [
            new PrivateChannel('chat.' . $this->message->conversation_id),
        ];
    }

    /**
     * الاسم الذي سيستمع إليه الـ Front-end في الجافاسكريبت
     */
    public function broadcastAs(): string
    {
        return 'message.new';
    }
}
