<?php

namespace App\Services;

use App\Repositories\UserRepository;

class AdminService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function assignRole(array $data)
    {
        try {
            $userId = $data['user_id'];
            $roleName = $data['role_name'];
            $user = $this->userRepo->findById($userId);
            if ($user->hasRole($roleName)) {
                return ['already_has_role' => true, 'user' => $user];
            }
            $this->userRepo->syncUserRoles($user, $roleName);
            return ['already_has_role' => false, 'user' => $user];
        } catch (\Exception $e) {
            throw new \Exception("Error assigning role: " . $e->getMessage());
        }
    }
    public function revokeRole(array $data)
    {
        try {
            $userId = $data['user_id'];
            $roleName = $data['role_name'];
            $user = $this->userRepo->findById($userId);

            if (!$user->hasRole($roleName)) {
                return [
                    'status' => 'not_found',
                    'user' => $user
                ];
            }

            $this->userRepo->removeUserRole($user, $roleName);

            return [
                'status' => 'success',
                'user' => $user
            ];
        } catch (\Exception $e) {
            throw new \Exception("Error revoking role: " . $e->getMessage());
        }
    }
    public function updateExistingRole(array $data)
    {
        try {
            $userId = $data['user_id'];
            $newRoleName = $data['role_name'];
            $user = $this->userRepo->findById($userId);
            $this->userRepo->syncUserRole($user, $newRoleName);
            return $user;
        } catch (\Exception $e) {
            throw new \Exception("Error updating role: " . $e->getMessage());
        }
    }
}
