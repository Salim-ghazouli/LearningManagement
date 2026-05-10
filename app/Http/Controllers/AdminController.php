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
            $result = $this->adminService->assignRole($request->all());
            if ($result['already_has_role']) {
                return $this->apiResponse(null, "User already has the role.", 200);
            }
            return $this->apiResponse(null, "Role assigned successfully", 200);
        } catch (Exception $e) {
            return $this->apiResponse(null, "Failed to assign role: " . $e->getMessage(), 500);
        }
    }
    public function revokeRole(AssignRoleRequest $request)
    {
        try {
            $result = $this->adminService->revokeRole($request->all());

            if ($result['status'] === 'not_found') {
                return $this->apiResponse(null, "User does not have the  role.", 404);
            }

            return $this->apiResponse(null, "Role  revoked successfully.", 200);
        } catch (Exception $e) {
            return $this->apiResponse(null, "Failed to revoke role: " . $e->getMessage(), 500);
        }
    }
    public function updateRole(AssignRoleRequest $request)
    {
        try {
            $user = $this->adminService->updateExistingRole($request->all());

            return $this->apiResponse(null, "User role has been updated to  successfully.", 200);
        } catch (\Exception $e) {
            return $this->apiResponse("Failed to update role: " . $e->getMessage(), 500);
        }
    }
}
