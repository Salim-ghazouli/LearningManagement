<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\InstructorProfile;
use App\Models\StudentProfile;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function registerUser(array $data)
    {
        try {
            $data['password'] = Hash::make($data['password']);
            $user = $this->userRepository->create($data);

            if (!empty($data['role'])) {
                $roleName = Str::title($data['role']);

                if (Role::where('name', $roleName)->exists()) {
                    $user->syncRoles([$roleName]);
                }

                if ($roleName === 'Instructor') {
                    InstructorProfile::create(['user_id' => $user->id]);
                } elseif ($roleName === 'Student') {
                    StudentProfile::create(['user_id' => $user->id]);
                }
            }

            $user->sendEmailVerificationNotification();

            return [
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken,
            ];
        } catch (\Exception $e) {
            throw new \Exception("Error registering user: " . $e->getMessage());
        }
    }

    public function loginUser(string $username, string $password)
    {
        try {
            $user = $this->userRepository->findByUsername($username);

            if (!$user || !Hash::check($password, $user->password)) {
                return null;
            }

            return [
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken
            ];
        } catch (\Exception $e) {
            throw new \Exception("Error logging in user: " . $e->getMessage());
        }
    }

    public function logoutUser($user)
    {
        try {
            return $user->currentAccessToken()->delete();
        } catch (\Exception $e) {
            throw new \Exception("Error logging out user: " . $e->getMessage());
        }
    }

    public function sendResetLink(array $data)
    {
        try {
            return Password::sendResetLink($data);
        } catch (\Exception $e) {
            throw new \Exception("Error sending reset link: " . $e->getMessage());
        }
    }

    public function resetPassword(array $data)
    {
        try {
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
        } catch (\Exception $e) {
            throw new \Exception("Error resetting password: " . $e->getMessage());
        }
    }
}
