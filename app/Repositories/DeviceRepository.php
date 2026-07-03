<?php

namespace App\Repositories;

use App\Models\UserDevice;

class DeviceRepository
{

    public function updateOrCreateToken($userId, string $fcmToken, ?string $deviceType = null)
    {
        return UserDevice::updateOrCreate(
            ['fcm_token' => $fcmToken],
            [
                'user_id'     => $userId,
                'device_type' => $deviceType
            ]
        );
    }


    public function getUserTokens($userId)
    {
        return UserDevice::where('user_id', $userId)->pluck('fcm_token')->toArray();
    }
    public function deleteToken($userId, string $fcmToken)
    {
        return UserDevice::where('user_id', $userId)
            ->where('fcm_token', $fcmToken)
            ->delete();
    }
}
