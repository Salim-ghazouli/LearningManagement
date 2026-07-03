<?php

namespace App\Services;

use App\Repositories\ChatRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class ChatService
{
    protected $chatRepo;

    public function __construct(ChatRepository $chatRepo)
    {
        $this->chatRepo = $chatRepo;
    }

    /**
     * منطق بدء أو جلب المحادثة
     */
    public function getOrCreateConversation(int $receiverId)
    {
        $senderId = Auth::id();

        // استخدام الـ Transaction لضمان عدم حدوث Race Condition أثناء إنشاء المحادثة
        return DB::transaction(function () use ($senderId, $receiverId) {
            $conversation = $this->chatRepo->findConversation($senderId, $receiverId);

            if (!$conversation) {
                $conversation = $this->chatRepo->createConversation($senderId, $receiverId);
            }

            return $conversation;
        });
    }

    /**
     * منطق إرسال رسالة وبثها
     */
    public function storeMessage(int $conversationId, string $text)
    {
        $senderId = Auth::id();

        // نفتح الـ Transaction مرة واحدة فقط لحماية العملية بالكامل
        return DB::transaction(function () use ($conversationId, $senderId, $text) {

            // 1. جلب المحادثة للتأكد من وجودها
            $conversation = $this->chatRepo->getConversationById($conversationId);

            // 2. صلاحيات الأمان: التأكد أن المستخدم طرف في المحادثة
            if ($conversation->sender_id !== $senderId && $conversation->receiver_id !== $senderId) {
                throw new Exception("Unauthorized to access this conversation.", 403);
            }

            // 3. إنشاء الرسالة وحفظها عبر المستودع (Repository)
            $message = $this->chatRepo->createMessage($conversationId, $senderId, $text);

            // 4. 🔥 البث الفوري عبر Pusher لجميع أطراف المحادثة ما عدا المرسل
            broadcast(new \App\Events\MessageSent($message))->toOthers();

            // 5. إرجاع كائن الرسالة بعد نجاح العملية بالكامل
            return $message;
        });
    }

    /**
     * منطق جلب تاريخ الرسائل
     */
    public function getConversationMessages($conversation_id)
    {
        $conversation = $this->chatRepo->getConversationById($conversation_id);

        $userId = Auth::id();

        if ($conversation->sender_id !== $userId && $conversation->receiver_id !== $userId) {
            throw new Exception("Unauthorized to access this conversation.", 403);
        }

        return $this->chatRepo->getMessagesByConversation($conversation_id);
    }
}
