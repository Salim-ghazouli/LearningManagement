<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected array $formData = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->formData = $data;

        return $data;
    }

    protected function afterSave(): void
    {
        if (!empty($this->formData['role'])) {
            $this->record->syncRoles([Str::title($this->formData['role'])]);
        }
    }
}
