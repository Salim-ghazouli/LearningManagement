<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'studentsen';

    protected static ?string $recordTitleAttribute = 'full_name';
}
