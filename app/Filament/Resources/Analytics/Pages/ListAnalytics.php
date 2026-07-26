<?php

namespace App\Filament\Resources\Analytics\Pages;

use App\Filament\Resources\Analytics\AnalyticsResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\AnalyticsOverview;

class ListAnalytics extends ListRecords
{
    protected static string $resource = AnalyticsResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            AnalyticsOverview::class,
        ];
    }
}
