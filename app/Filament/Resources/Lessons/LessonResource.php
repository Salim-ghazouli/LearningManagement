<?php

namespace App\Filament\Resources\Lessons;

use App\Filament\Resources\Lessons\Pages;
use App\Models\Lesson;
use App\Models\Course;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationLabel = 'Lessons';

    protected static string | UnitEnum | null $navigationGroup = 'Core Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('course_id')
                    ->label('Course')
                    ->options(Course::pluck('title', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('title')
                    ->label('Lesson Title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->required(),

                

                Forms\Components\RichEditor::make('description')
                    ->label('Description')
                    ->maxLength(65535)
                    ->columnSpanFull(),

            Forms\Components\FileUpload::make('lesson_files')
                    ->label('Lesson Files (Video / PDF)')
                    ->multiple()
                    ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'application/pdf'])
                    ->maxSize(102400)
                    ->preserveFilenames()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->label('Order')
                    ->sortable(),

                

                Tables\Columns\TextColumn::make('media')
                    ->label('Files & Sizes')
                    ->formatStateUsing(function ($record) {
                        return $record->getMedia('lessons')->map(function ($media) {
                            $sizeInMb = number_format($media->size / (1024 * 1024), 2);
                            return "{$media->file_name} ({$sizeInMb} MB)";
                        })->join(', ');
                    })
                    ->wrap()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Filter by Course')
                    ->options(Course::pluck('title', 'id'))
                    ->searchable(),

                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLessons::route('/'),
            'create' => Pages\CreateLesson::route('/create'),
            'edit' => Pages\EditLesson::route('/{record}/edit'),
        ];
    }
}
