<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Models\InstructorProfile;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function registerUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);
        $user->sendEmailVerificationNotification();
        if ($user->role_id == 2) {
            InstructorProfile::create(['user_id' => $user->id]);
        } elseif ($user->role_id == 3) {
            StudentProfile::create(['user_id' => $user->id]);
        }

        return [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ];
    }

    public function loginUser(string $username, string $password)
    {
        $user = $this->userRepository->findByUsername($username);

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        return [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ];
    }

    public function logoutUser($user)
    {
        return $user->currentAccessToken()->delete();
    }

    public function sendResetLink(array $data)
    {
        return Password::sendResetLink($data);
    }

    public function resetPassword(array $data)
    {
       return Password::broker()->reset(
        $data,
        function ($user, $password) {
            if (!$user) {
                throw new \Exception("User not found during password reset.");
            }

            $user->password = Hash::make($password);
            $user->setRememberToken(Str::random(60));
            $user->save();

            event(new PasswordReset($user));
        }
    );
    }
}
