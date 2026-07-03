<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\StartConversationRequest;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Http\Requests\Chat\getMessagesRequest;
use App\Services\ChatService;
use App\Traits\ApiResponseTrait;
use Exception;

class ChatController extends Controller
{
    protected $chatService;
    use ApiResponseTrait;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function startConversation(StartConversationRequest $request)
    {
        $conversation = $this->chatService->getOrCreateConversation($request->receiver_id);

        return self::apiResponse([
            'status' => true,
            'message' => 'Conversation ready',
            'data' => $conversation
        ], 200);
    }

    public function sendMessage(SendMessageRequest $request)
    {
        try {
            $message = $this->chatService->storeMessage($request->conversation_id, $request->message);

            return self::apiResponse([
                'status' => true,
                'message' => 'Message sent successfully',
                'data' => $message
            ], 201);
        } catch (Exception $e) {
            return self::apiResponse([
                'status' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }

    public function getMessages(getMessagesRequest $request)
    {
        try {
            $messages = $this->chatService->getConversationMessages($request->conversation_id);

            return self::apiResponse([
                'status' => true,
                'data' => $messages
            ], 200);
        } catch (Exception $e) {
            return self::apiResponse([
                'status' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }
}
