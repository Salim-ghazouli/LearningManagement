<?php

namespace App\Services;

use App\Repositories\DeviceRepository;
use Kreait\Firebase\Messaging\CloudMessage; // استيراد كلاس الرسالة
use Kreait\Firebase\Messaging\Notification; // استيراد كلاس الإشعار
use Exception;

class FirebaseNotificationService
{
    protected $deviceRepo;

    public function __construct(DeviceRepository $deviceRepo)
    {
        $this->deviceRepo = $deviceRepo;
    }


    public function sendToUser($userId, string $title, string $body, array $data = [])
    {
        $tokens = $this->deviceRepo->getUserTokens($userId);

        if (empty($tokens)) {
            throw new Exception("No registered devices found for User ID: {$userId}");
        }

        try {
            if (!app()->bound('firebase.messaging')) {
                throw new Exception("Firebase Service Provider is not registered properly.");
            }

            $messaging = app('firebase.messaging');

            $notification = Notification::create($title, $body);


            foreach ($tokens as $token) {
                try {
                    $message = CloudMessage::new()
                        ->withToken($token)
                        ->withNotification($notification)
                        ->withData($data);

                    $messaging->send($message);
                } catch (\Exception $e) {
                    if (str_contains($e->getMessage(), 'not a valid') || str_contains($e->getMessage(), 'Requested entity was not found')) {
                        \App\Models\UserDevice::where('fcm_token', $token)->delete();
                        throw new Exception("Cleaned up invalid FCM token from DB: {$token}");
                    } else {
                        throw new Exception("Failed to send Firebase notification: " . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            throw new Exception("Global Firebase Notification Error: " . $e->getMessage());
        }
    }
}
