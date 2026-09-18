<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PageViewStats extends StatsOverviewWidget
{
    protected array|int|null $columns = [
        'default' => 1,
        'md' => 2,
        'lg' => 2,
    ];

    protected function getStats(): array
    {
        $today = PageView::query()->today()->count();
        $allTime = PageView::query()->count();

        return [
            Stat::make('View event hari ini', $today)
                ->description('Total lihat halaman detail event')
                ->color('success'),
            Stat::make('Total view event', $allTime)
                ->description('Keseluruhan halaman detail event')
                ->color('info'),
        ];
    }
}
