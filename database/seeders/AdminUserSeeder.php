<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'full_name' => 'Admin',
                'username' => 'Admin',
                'password' => Hash::make('admin1234'), 
                'password_confirmation' => Hash::make('admin1234')
            ]
        );

        $admin->assignRole('Admin');
    }
}