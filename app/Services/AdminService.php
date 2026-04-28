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
}
