<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignRoleRequest;
use App\Services\AdminService;
use App\Traits\ApiResponseTrait;
use Exception;

class AdminController extends Controller
{
    use ApiResponseTrait;

    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function assignRole(AssignRoleRequest $request)
    {
        try {
            $result = $this->adminService->assignRole(
                $request->user_id,
                $request->role_name
            );
            if ($result['already_has_role']) {
                return $this->apiResponse(null, "User already has the role.", 200);
            }
            return $this->apiResponse(null, "Role assigned successfully", 200);
        } catch (Exception $e) {
            return $this->apiResponse(null, "Failed to assign role: " . $e->getMessage(), 400);
        }
    }
}
