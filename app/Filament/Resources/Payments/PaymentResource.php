<?php

namespace App\Filament\Resources\Payments;

use App\Filament\Resources\Payments\Pages;
use App\Models\Transaction;
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
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use UnitEnum;

class PaymentResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Payments';
    protected static string | UnitEnum | null $navigationGroup = 'Billing & Payments';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('stripe_session_id')
                ->label('Stripe Session ID')
                ->disabled()
                ->columnSpanFull(),

            Forms\Components\Select::make('user_id')
                ->relationship('user', 'full_name')
                ->label('Student')
                ->searchable()
                ->preload()
                ->disabled(),

            Forms\Components\Select::make('course_id')
                ->relationship('course', 'title')
                ->label('Course')
                ->searchable()
                ->preload()
                ->disabled(),

            Forms\Components\TextInput::make('amount')
                ->label('Amount')
                ->numeric()
                ->disabled(),

            Forms\Components\TextInput::make('currency')
                ->label('Currency')
                ->disabled(),

            Forms\Components\Select::make('status')
                ->label('Payment Status')
                ->options([
                    'pending' => 'Pending',
                    'completed' => 'Completed',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ])
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.full_name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('stripe_session_id')
                    ->label('Stripe Session ID')
                    ->searchable()
                    ->copyable()
                    ->limit(30),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('usd')
                    ->sortable(),

                TextColumn::make('currency')
                    ->label('Currency')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'info' => 'refunded',
                    ])
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->sortable(),

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
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),

                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'full_name')
                    ->label('Student'),

                Tables\Filters\SelectFilter::make('course_id')
                    ->relationship('course', 'title')
                    ->label('Course'),
            ])
            ->actions([
                Action::make('refund')
                    ->label('Refund Payment')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->visible(fn(Transaction $record) => $record->status === 'completed')
                    ->action(function (Transaction $record) {
                        try {
                            // Mock refund logic (replace with real Stripe API call if needed)
                            $record->update(['status' => 'refunded']);

                            Notification::make()
                                ->success()
                                ->title('Payment Refunded')
                                ->body("Payment of {$record->currency} {$record->amount} has been refunded.")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Refund Failed')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),

                Action::make('viewDetails')
                    ->label('View')
                    ->url(fn(Transaction $record) => route('filament.admin.resources.payments.view', $record))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListPayments::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
