<?php

namespace App\Filament\Pages;

use App\Models\Property;
use App\Services\ClassificationService;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Analytics extends Page
{
    protected static ?string $title = 'Analytics';
    protected static ?string $navigationLabel = 'Analytics';
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::ChartBarSquare;

    protected string $view = 'filament.pages.analytics';

    public int $totalProperties = 0;
    public float $avgPrice = 0;
    public float $maxPrice = 0;
    public float $minPrice = 0;
    public float $avgArea = 0;
    public float $medianPrice = 0;

    public array $priceByCity = [];
    public array $priceByLegalStatus = [];
    public array $priceByFurniture = [];
    public array $priceByBedrooms = [];
    public array $priceSegmentDistribution = [];
    public array $areaVsPrice = [];
    public array $correlationData = [];
    public array $modelMetrics = [];

    public function mount(): void
    {
        $this->loadStatistics();
        $this->loadChartData();
        $this->loadCorrelationData();
        
        $classificationService = new ClassificationService();
        $this->modelMetrics = $classificationService->getModelMetrics();
    }

    protected function loadStatistics(): void
    {
        $this->totalProperties = Property::count();
        $this->avgPrice = Property::avg('price') ?? 0;
        $this->maxPrice = Property::max('price') ?? 0;
        $this->minPrice = Property::min('price') ?? 0;
        $this->avgArea = Property::avg('area') ?? 0;
        
        // Calculate median in PHP (MySQL doesn't support PERCENTILE_CONT)
        $prices = Property::pluck('price')->toArray();
        sort($prices);
        $count = count($prices);
        if ($count > 0) {
            $middle = (int) floor($count / 2);
            $this->medianPrice = $count % 2 ? $prices[$middle] : ($prices[$middle - 1] + $prices[$middle]) / 2;
        } else {
            $this->medianPrice = 0;
        }

        $this->priceByCity = Property::select('city')
            ->selectRaw('AVG(price) as avg_price, COUNT(*) as count')
            ->groupBy('city')
            ->orderByDesc('avg_price')
            ->limit(10)
            ->get()
            ->toArray();

        $this->priceByLegalStatus = Property::select('legal_status')
            ->selectRaw('AVG(price) as avg_price, COUNT(*) as count')
            ->groupBy('legal_status')
            ->orderByDesc('avg_price')
            ->get()
            ->toArray();

        $this->priceByFurniture = Property::select('furniture_state')
            ->selectRaw('AVG(price) as avg_price, COUNT(*) as count')
            ->groupBy('furniture_state')
            ->orderByDesc('avg_price')
            ->get()
            ->toArray();

        $this->priceByBedrooms = Property::select('bedrooms')
            ->selectRaw('AVG(price) as avg_price, COUNT(*) as count')
            ->groupBy('bedrooms')
            ->orderBy('bedrooms')
            ->whereNotNull('bedrooms')
            ->get()
            ->toArray();

        $this->priceSegmentDistribution = [
            'low' => Property::where('price_segment', 'Low Price')->count(),
            'medium' => Property::where('price_segment', 'Medium Price')->count(),
            'high' => Property::where('price_segment', 'High Price')->count(),
        ];
    }

    protected function loadChartData(): void
    {
        // Sample for scatter plot (Area vs Price)
        $samples = Property::select('area', 'price')
            ->whereNotNull('area')
            ->whereNotNull('price')
            ->where('area', '>', 0)
            ->where('price', '>', 0)
            ->limit(500)
            ->get()
            ->map(fn($p) => [(float)$p->area, (float)$p->price])
            ->toArray();
        
        $this->areaVsPrice = $samples;
    }

    protected function loadCorrelationData(): void
    {
        // Calculate correlation for numeric features
        $properties = Property::select('area', 'frontage', 'access_road', 'floors', 'bedrooms', 'bathrooms', 'price')
            ->whereNotNull('area')
            ->whereNotNull('price')
            ->where('area', '>', 0)
            ->where('price', '>', 0)
            ->limit(1000)
            ->get();

        $columns = ['area', 'frontage', 'access_road', 'floors', 'bedrooms', 'bathrooms', 'price'];
        $n = count($columns);
        
        // Calculate correlation matrix
        $matrix = [];
        foreach ($columns as $i => $col1) {
            $matrix[$col1] = [];
            foreach ($columns as $j => $col2) {
                $values1 = $properties->pluck($col1)->map(fn($v) => (float)($v ?? 0))->toArray();
                $values2 = $properties->pluck($col2)->map(fn($v) => (float)($v ?? 0))->toArray();
                
                $matrix[$col1][$col2] = $this->calculateCorrelation($values1, $values2);
            }
        }
        
        $this->correlationData = [
            'matrix' => $matrix,
            'labels' => ['Diện tích', 'Mặt tiền', 'Đường vào', 'Tầng', 'PN', 'PT', 'Giá'],
        ];
    }

    protected function calculateCorrelation(array $x, array $y): float
    {
        $n = count($x);
        if ($n < 2) return 0;
        
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumX2 = 0;
        $sumY2 = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumX2 += $x[$i] * $x[$i];
            $sumY2 += $y[$i] * $y[$i];
        }
        
        $numerator = $n * $sumXY - $sumX * $sumY;
        $denominator = sqrt(($n * $sumX2 - $sumX * $sumX) * ($n * $sumY2 - $sumY * $sumY));
        
        if ($denominator == 0) return 0;
        
        return round($numerator / $denominator, 3);
    }

    public function getChartLabels(): array
    {
        return array_map(fn($item) => $item['city'] ?? 'N/A', $this->priceByCity);
    }

    public function getChartPrices(): array
    {
        return array_map(fn($item) => round(($item['avg_price'] ?? 0) / 1e9, 2), $this->priceByCity);
    }

    public function getSegmentPercentages(): array
    {
        $total = $this->totalProperties ?: 1;
        return [
            'low' => round(($this->priceSegmentDistribution['low'] ?? 0) / $total * 100, 1),
            'medium' => round(($this->priceSegmentDistribution['medium'] ?? 0) / $total * 100, 1),
            'high' => round(($this->priceSegmentDistribution['high'] ?? 0) / $total * 100, 1),
        ];
    }
}
