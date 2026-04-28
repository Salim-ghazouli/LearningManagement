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

    public function updateRole($userId, $roleName)
    {
        $user = $this->userRepo->findById($userId);
        return $this->userRepo->syncUserRoles($user, $roleName);
    }
}