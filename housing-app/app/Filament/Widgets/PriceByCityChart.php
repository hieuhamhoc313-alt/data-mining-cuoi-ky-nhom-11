<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Widgets\ChartWidget;

class PriceByCityChart extends ChartWidget
{
    protected ?string $heading = 'Giá trung bình theo thành phố';

    protected function getData(): array
    {
        $cities = Property::select('city')
            ->selectRaw('AVG(price) as avg_price')
            ->groupBy('city')
            ->orderByDesc('avg_price')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Giá trung bình (VNĐ)',
                    'data' => $cities->pluck('avg_price')->map(fn ($price) => $price / 1000000000)->toArray(),
                    'backgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => $cities->pluck('city')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Giá (Tỷ VNĐ)',
                    ],
                ],
            ],
        ];
    }
}
