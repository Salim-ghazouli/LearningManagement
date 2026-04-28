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

    public function assignRole($userId, $roleName)
    {
        $user = $this->userRepo->findById($userId);
        if ($user->hasRole($roleName)) {
            return ['already_has_role' => true, 'user' => $user];
        }
        $this->userRepo->syncUserRoles($user, $roleName);
        return ['already_has_role' => false, 'user' => $user];
    }
    public function revokeRole($userId, $roleName)
    {
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
    }
    public function updateExistingRole($userId, $newRoleName)
    {
        $user = $this->userRepo->findById($userId);
        $this->userRepo->syncUserRole($user, $newRoleName);
        return $user;
    }
}
