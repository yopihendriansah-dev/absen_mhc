<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Registration;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MhcStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Event aktif', Event::query()->where('status', Event::STATUS_PUBLISHED)->count())
                ->description('Event yang sedang menerima pendaftaran')
                ->color('success'),
            Stat::make('Total peserta', Registration::query()->where('status', Registration::STATUS_REGISTERED)->count())
                ->description('Seluruh event')
                ->color('warning'),
            Stat::make('Total hadir', Attendance::query()->count())
                ->description('Check-in tercatat')
                ->color('info'),
        ];
    }
}
