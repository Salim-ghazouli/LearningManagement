<?php

namespace App\Filament\Resources\Enrollments;

use App\Filament\Resources\Enrollments\Pages;
use App\Models\Enrollment;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;


    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Enrollments';


    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'full_name')
                ->label('Student')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\Select::make('course_id')
                ->relationship('course', 'title')
                ->label('Course')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'active' => 'Active',
                    'expired' => 'Expired',
                    'pending' => 'Pending',
                ])
                ->default('pending')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.full_name')->label('Student')->searchable()->sortable(),
                TextColumn::make('course.title')->label('Course')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'expired' => 'danger',
                        'pending' => 'warning',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->sortable(),
                TextColumn::make('created_at')->label('Enrolled')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'pending' => 'Pending',
                    ]),
            ])
            ->actions([
                Action::make('markActive')
                    ->label('Mark Active')
                    ->action(fn(Enrollment $record) => $record->update(['status' => 'active']))
                    ->requiresConfirmation(),

                Action::make('markExpired')
                    ->label('Mark Expired')
                    ->action(fn(Enrollment $record) => $record->update(['status' => 'expired']))
                    ->requiresConfirmation(),

                EditAction::make(),
                DeleteAction::make()->label('Cancel Enrollment'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
