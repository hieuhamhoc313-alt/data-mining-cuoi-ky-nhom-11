<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Widgets\ChartWidget;

class PriceByFurnitureChart extends ChartWidget
{
    protected ?string $heading = 'Giá trung bình theo nội thất';

    protected function getData(): array
    {
        $furnitureStates = Property::select('furniture_state')
            ->selectRaw('AVG(price) as avg_price')
            ->groupBy('furniture_state')
            ->orderByDesc('avg_price')
            ->get();

        $colors = [
            '#8b5cf6',
            '#06b6d4',
            '#f59e0b',
            '#ec4899',
            '#ef4444',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Giá trung bình (VNĐ)',
                    'data' => $furnitureStates->pluck('avg_price')->map(fn ($price) => $price / 1000000000)->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $furnitureStates->count()),
                ],
            ],
            'labels' => $furnitureStates->pluck('furniture_state')->toArray(),
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
