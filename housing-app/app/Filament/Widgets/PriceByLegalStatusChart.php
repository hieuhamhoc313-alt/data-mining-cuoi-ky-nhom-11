<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Widgets\ChartWidget;

class PriceByLegalStatusChart extends ChartWidget
{
    protected ?string $heading = 'Giá trung bình theo pháp lý';

    protected function getData(): array
    {
        $legalStatuses = Property::select('legal_status')
            ->selectRaw('AVG(price) as avg_price')
            ->groupBy('legal_status')
            ->orderByDesc('avg_price')
            ->get();

        $colors = [
            '#22c55e',
            '#3b82f6',
            '#f59e0b',
            '#ef4444',
            '#8b5cf6',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Giá trung bình (VNĐ)',
                    'data' => $legalStatuses->pluck('avg_price')->map(fn ($price) => $price / 1000000000)->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $legalStatuses->count()),
                ],
            ],
            'labels' => $legalStatuses->pluck('legal_status')->toArray(),
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
