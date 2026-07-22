<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\AuthService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $authService = app(AuthService::class);

        $result = $authService->registerUser($data);

        if (!empty($data['role'])) {
            $result['user']->syncRoles([Str::title($data['role'])]);
        }

        return $result['user'];
    }
}
