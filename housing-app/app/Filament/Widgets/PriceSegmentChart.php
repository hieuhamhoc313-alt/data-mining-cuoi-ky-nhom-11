<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Widgets\ChartWidget;

class PriceSegmentChart extends ChartWidget
{
    protected ?string $heading = 'Phân bố phân khúc giá';

    protected function getData(): array
    {
        $lowCount = Property::where('price_segment', 'Low Price')->count();
        $mediumCount = Property::where('price_segment', 'Medium Price')->count();
        $highCount = Property::where('price_segment', 'High Price')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Số lượng BĐS',
                    'data' => [$lowCount, $mediumCount, $highCount],
                    'backgroundColor' => [
                        '#ef4444',
                        '#f59e0b',
                        '#22c55e',
                    ],
                ],
            ],
            'labels' => ['Low Price', 'Medium Price', 'High Price'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
