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

    
    public function getOrCreateConversation(int $receiverId)
    {
        $senderId = Auth::id();

        return DB::transaction(function () use ($senderId, $receiverId) {
            $conversation = $this->chatRepo->findConversation($senderId, $receiverId);

            if (!$conversation) {
                $conversation = $this->chatRepo->createConversation($senderId, $receiverId);
            }

            return $conversation;
        });
    }

    public function storeMessage(int $conversationId, string $text)
    {
        $senderId = Auth::id();

        return DB::transaction(function () use ($conversationId, $senderId, $text) {

            $conversation = $this->chatRepo->getConversationById($conversationId);

            if ($conversation->sender_id !== $senderId && $conversation->receiver_id !== $senderId) {
                throw new Exception("Unauthorized to access this conversation.", 403);
            }

            $message = $this->chatRepo->createMessage($conversationId, $senderId, $text);

            broadcast(new \App\Events\MessageSent($message))->toOthers();

            return $message;
        });
    }

    
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
