<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\DeviceRepository;
use App\Http\Requests\Notification_tokne\StoreDeviceTokenRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\User;

class DeviceController extends Controller
{
    use ApiResponseTrait;

    protected $deviceRepo;

    public function __construct(DeviceRepository $deviceRepo)
    {
        $this->deviceRepo = $deviceRepo;
    }

    
    public function storeToken(StoreDeviceTokenRequest $request)
    {
        try {
            if (!Auth::check()) {
                return self::apiResponse(null, 'Unauthorized', 401);
            }
            $user = User::find(Auth::id());
            $userId = $user->id;
            $fcmToken = $request->input('fcm_token');
            $deviceType = $request->input('device_type'); 

            $device = $this->deviceRepo->updateOrCreateToken($userId, $fcmToken, $deviceType);

            return self::apiResponse($device, 'Device token registered successfully.', 200);
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), 400);
        }
    }
}
