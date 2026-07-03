<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});



/**
 * التحقق من صلاحية المستخدم للاستماع إلى قناة المحادثة الخاصة
 */
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    // جلب المحادثة للتأكد من أطرافها
    $conversation = Conversation::find($conversationId);

    if (!$conversation) {
        return false;
    }

    // السماح فقط إذا كان المستخدم الحالي هو المرسل أو المستقبل للمحادثة
    return (int) $user->id === (int) $conversation->sender_id ||
        (int) $user->id === (int) $conversation->receiver_id;
});