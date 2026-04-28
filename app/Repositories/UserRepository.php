<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function create(array $data)
    {
        return User::create($data);
    }

    public function findByUsername(string $username)
    {
        return User::where('username', $username)->first();
    }

    public function findById($id)
    {
        return User::findOrFail($id);
    }

    public function syncUserRoles(User $user, string $roleName)
    {
        return $user->syncRoles([$roleName]);
    }
}
