<?php

namespace App\Repositories;

use App\Models\Conversation;
use App\Models\Message;

class ChatRepository
{
    /**
     * البحث عن محادثة ثنائية قائمة بين مستخدمين
     */
    public function findConversation(int $userId1, int $userId2)
    {
        return Conversation::where(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId1)->where('receiver_id', $userId2);
        })->orWhere(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId2)->where('receiver_id', $userId1);
        })->first();
    }

    /**
     * إنشاء محادثة جديدة
     */
    public function createConversation(int $senderId, int $receiverId)
    {
        return Conversation::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId
        ]);
    }

    /**
     * جلب محادثة محددة مع التحقق من وجودها
     */
    public function getConversationById($conversation_id)
    {
        return Conversation::findOrFail($conversation_id);
    }

    /**
     * حفظ الرسالة في قاعدة البيانات
     */
    public function createMessage(int $conversation_id, int $senderId, string $text)
    {
        return Message::create([
            'conversation_id' => $conversation_id,
            'sender_id' => $senderId,
            'message' => $text,
        ]);
    }

    /**
     * جلب رسائل محادثة معينة مع بيانات المرسل
     */
    public function getMessagesByConversation($conversation_id)
    {
        return Message::where('conversation_id', $conversation_id)
            ->with('sender:id')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
