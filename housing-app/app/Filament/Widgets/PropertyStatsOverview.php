<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PropertyStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProperties = Property::count();
        $avgPrice = Property::avg('price');
        $maxPrice = Property::max('price');
        $minPrice = Property::min('price');

        return [
            Stat::make('Tổng số BĐS', number_format($totalProperties))
                ->description('Bất động sản trong hệ thống')
                ->descriptionIcon(Heroicon::BuildingOffice)
                ->color('amber'),
            Stat::make('Giá trung bình', number_format($avgPrice, 0, ',', '.') . ' ₫')
                ->description('Giá trung bình')
                ->descriptionIcon(Heroicon::CurrencyDollar)
                ->color('amber'),
            Stat::make('Giá cao nhất', number_format($maxPrice, 0, ',', '.') . ' ₫')
                ->description(number_format($maxPrice / 1000000000, 1) . ' tỷ VNĐ')
                ->descriptionIcon(Heroicon::ArrowTrendingUp)
                ->color('success'),
            Stat::make('Giá thấp nhất', number_format($minPrice, 0, ',', '.') . ' ₫')
                ->description(number_format($minPrice / 1000000, 0) . ' triệu VNĐ')
                ->descriptionIcon(Heroicon::ArrowTrendingDown)
                ->color('danger'),
        ];
    }
}
