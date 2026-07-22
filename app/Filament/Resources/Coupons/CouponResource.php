<?php

namespace App\Filament\Resources\Coupons;

use App\Filament\Resources\Coupons\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Notifications\Notification;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Coupons';
    protected static string | UnitEnum | null $navigationGroup = 'Billing & Payments';

    // كتبنا المسار الكامل للـ Schema هنا مباشرة لضمان عدم التعارض مع الفورم أو الجداول
    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('code')
                ->label('Coupon Code')
                ->required()
                ->unique(ignoreRecord: true)
                ->uppercase()
                ->maxLength(255)
                ->columnSpanFull(),

            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->rows(3)
                ->columnSpanFull(),

            Forms\Components\Select::make('type')
                ->label('Discount Type')
                ->options([
                    'percentage' => 'Percentage (%)',
                    'fixed' => 'Fixed Amount ($)',
                ])
                ->required(),

            Forms\Components\TextInput::make('value')
                ->label('Discount Value')
                ->numeric()
                ->minValue(0)
                ->step(0.01)
                ->required(),

            Forms\Components\DateTimePicker::make('starts_at')
                ->label('Start Date')
                ->nullable(),

            Forms\Components\DateTimePicker::make('expires_at')
                ->label('Expiration Date')
                ->nullable(),

            Forms\Components\TextInput::make('usage_limit')
                ->label('Usage Limit')
                ->numeric()
                ->minValue(1)
                ->nullable()
                ->helperText('Leave empty for unlimited usage'),

            Forms\Components\Toggle::make('is_global')
                ->label('Global Coupon')
                ->helperText('Available for all courses if enabled')
                ->default(true)
                ->live(),

            Forms\Components\Select::make('courses')
                ->label('Assign to Courses')
                ->relationship('courses', 'title')
                ->multiple()
                ->searchable()
                ->preload()
                ->hidden(fn($get) => $get('is_global')),

            Forms\Components\Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'percentage' => 'info',
                        'fixed' => 'warning',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->formatStateUsing(
                        fn($record): string =>
                        $record && $record->type === 'percentage'
                            ? $record->value . '%'
                            : '$' . number_format($record ? $record->value : 0, 2)
                    )
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_global')
                    ->label('Global')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('usage_limit')
                    ->label('Usage Limit')
                    ->formatStateUsing(fn(?int $state) => $state ? "$state uses" : 'Unlimited')
                    ->sortable(),

                Tables\Columns\TextColumn::make('used_count')
                    ->label('Used')
                    ->sortable(),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Starts')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed',
                    ]),

                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),

                Tables\Filters\SelectFilter::make('is_global')
                    ->options([
                        1 => 'Global',
                        0 => 'Course-Specific',
                    ]),
            ])
            ->actions([
                // استخدام كلاس الـ Action العام التابع لحزمة الفيلPartial الأساسية لمنع التعارض مع الجداول والـ Schema
                \Filament\Actions\Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn($record) => $record && !$record->is_active)
                    ->action(function ($record) {
                        if ($record) {
                            $record->update(['is_active' => true]);
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Coupon Activated')
                                ->body("Coupon {$record->code} is now active.")
                                ->send();
                        }
                    }),

                \Filament\Actions\Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record && $record->is_active)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        if ($record) {
                            $record->update(['is_active' => false]);
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Coupon Deactivated')
                                ->body("Coupon {$record->code} is now inactive.")
                                ->send();
                        }
                    }),

                EditAction::make(),
                DeleteAction::make(),
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
