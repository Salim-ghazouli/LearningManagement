<?php

namespace App\Filament\Resources\Courses;

use App\Filament\Resources\Courses\Pages;
use App\Models\Course;
use App\Models\User;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Courses';
    protected static string | UnitEnum | null $navigationGroup = 'Course Management';
    

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Course Title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('Course Description')
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\Select::make('instructors')
                    ->label('Instructor')
                    ->relationship('instructors', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required(),


                Forms\Components\TextInput::make('category')
                    ->label('Category')
                    ->maxLength(255),

                Forms\Components\TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->default(0),

                Forms\Components\Toggle::make('is_free')
                    ->label('Is Free Course')
                    ->default(false),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ])
                    ->default('draft')
                    ->required(),

                Forms\Components\FileUpload::make('course_image')
                    ->label('Course Image')
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16 / 9')
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                    ->disk('public')
                    ->directory('courses/images')
                    ->visibility('public')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('course_image')
                    ->label('Image')
                    ->width(100)
                    ->height(100)
                    ->disk('public'),

                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('instructors.full_name')
                    ->label('Instructor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->sortable()
                    ->formatStateUsing(fn($state) => '$' . number_format($state, 2)),

                TextColumn::make('is_free')
                    ->label('Type')
                    ->formatStateUsing(fn($state) => $state ? 'Free' : 'Paid'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                    ])
                    ->sortable(),

                TextColumn::make('enrolled_count')
                    ->label('Enrolled Students')
                    ->getStateUsing(function ($record) {
                        return $record->studentsen()->count();
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->withCount('studentsen')->orderBy('studentsen_count', $direction);
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),

                Tables\Filters\SelectFilter::make('instructors')
                    ->label('Instructor')
                    ->relationship('instructors', 'full_name'),

                Tables\Filters\SelectFilter::make('is_free')
                    ->label('Type')
                    ->options([
                        0 => 'Paid',
                        1 => 'Free',
                    ]),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
