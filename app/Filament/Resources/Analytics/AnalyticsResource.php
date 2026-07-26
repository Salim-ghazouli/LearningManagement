<?php

namespace App\Filament\Resources\Analytics;

use App\Filament\Resources\Analytics\Pages;
use App\Models\Course;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use BackedEnum;
use UnitEnum;

class AnalyticsResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Statistics & Analytics';
    protected static string | UnitEnum | null $navigationGroup = 'Billing & Payments';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Course::query()->withSum(['transactions' => function ($query) {
                    $query->where('status', 'completed');
                }], 'amount')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Course Title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Base Price')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('enrollments_count')
                    ->label('Total Enrollments')
                    ->counts('enrollments')
                    ->sortable(),

                TextColumn::make('transactions_sum_amount')
                    ->label('Revenue Per Course')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('$0.00'),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnalytics::route('/'),
        ];
    }
}
