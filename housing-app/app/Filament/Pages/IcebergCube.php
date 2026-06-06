<?php

namespace App\Filament\Pages;

use App\Models\Property;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class IcebergCube extends Page
{
    protected static ?string $title = 'Iceberg Cube';
    protected static ?string $navigationLabel = 'Iceberg Cube';
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::Cube;

    protected string $view = 'filament.pages.iceberg-cube';

    public array $icebergData = [];
    public array $cubeStats = [];
    public int $resultCount = 0;
    public array $aggregationLevels = [];
    public array $topCombinations = [];

    public function mount(): void
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        // Main Iceberg Cube Query (3 dimensions)
        $results = Property::select('city', 'legal_status', 'furniture_state')
            ->selectRaw('COUNT(*) as property_count, AVG(price) as avg_price, MIN(price) as min_price, MAX(price) as max_price, AVG(area) as avg_area')
            ->groupBy('city', 'legal_status', 'furniture_state')
            ->havingRaw('COUNT(*) > 20')
            ->orderBy('city')
            ->orderBy('legal_status')
            ->orderBy('furniture_state')
            ->get();

        $this->resultCount = $results->count();

        // Group by City
        $grouped = [];
        foreach ($results as $row) {
            $cityKey = $row->city ?? 'N/A';
            $legalKey = $row->legal_status ?? 'N/A';
            $furnitureKey = $row->furniture_state ?? 'N/A';

            if (!isset($grouped[$cityKey])) {
                $grouped[$cityKey] = [];
            }
            if (!isset($grouped[$cityKey][$legalKey])) {
                $grouped[$cityKey][$legalKey] = [];
            }
            $grouped[$cityKey][$legalKey][$furnitureKey] = [
                'count' => $row->property_count,
                'avg_price' => $row->avg_price,
                'min_price' => $row->min_price,
                'max_price' => $row->max_price,
                'avg_area' => $row->avg_area,
            ];
        }

        $this->icebergData = $grouped;

        // Calculate cube statistics
        $this->calculateCubeStats();

        // Get top combinations
        $this->topCombinations = Property::select('city', 'legal_status', 'furniture_state')
            ->selectRaw('COUNT(*) as count, AVG(price) as avg_price')
            ->groupBy('city', 'legal_status', 'furniture_state')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->toArray();
    }

    protected function calculateCubeStats(): void
    {
        // 1-Level Aggregations
        $byCity = Property::select('city')
            ->selectRaw('COUNT(*) as count, AVG(price) as avg_price')
            ->groupBy('city')
            ->havingRaw('COUNT(*) > 20')
            ->get();

        $byLegal = Property::select('legal_status')
            ->selectRaw('COUNT(*) as count, AVG(price) as avg_price')
            ->groupBy('legal_status')
            ->havingRaw('COUNT(*) > 20')
            ->get();

        $byFurniture = Property::select('furniture_state')
            ->selectRaw('COUNT(*) as count, AVG(price) as avg_price')
            ->groupBy('furniture_state')
            ->havingRaw('COUNT(*) > 20')
            ->get();

        // 2-Level Aggregations
        $byCityLegal = Property::select('city', 'legal_status')
            ->selectRaw('COUNT(*) as count, AVG(price) as avg_price')
            ->groupBy('city', 'legal_status')
            ->havingRaw('COUNT(*) > 20')
            ->get();

        $byCityFurniture = Property::select('city', 'furniture_state')
            ->selectRaw('COUNT(*) as count, AVG(price) as avg_price')
            ->groupBy('city', 'furniture_state')
            ->havingRaw('COUNT(*) > 20')
            ->get();

        $byLegalFurniture = Property::select('legal_status', 'furniture_state')
            ->selectRaw('COUNT(*) as count, AVG(price) as avg_price')
            ->groupBy('legal_status', 'furniture_state')
            ->havingRaw('COUNT(*) > 20')
            ->get();

        $this->aggregationLevels = [
            'city' => $byCity->count(),
            'legal' => $byLegal->count(),
            'furniture' => $byFurniture->count(),
            'city_legal' => $byCityLegal->count(),
            'city_furniture' => $byCityFurniture->count(),
            'legal_furniture' => $byLegalFurniture->count(),
            'city_legal_furniture' => $this->resultCount,
        ];

        $this->cubeStats = [
            'total_dimensions' => 3,
            'total_combinations' => pow(2, 3), // 2^3 = 8
            'iceberg_threshold' => 20,
            'level_1' => $byCity->count() + $byLegal->count() + $byFurniture->count(),
            'level_2' => $byCityLegal->count() + $byCityFurniture->count() + $byLegalFurniture->count(),
            'level_3' => $this->resultCount,
            'total_groups' => $byCity->count() + $byLegal->count() + $byFurniture->count() + 
                              $byCityLegal->count() + $byCityFurniture->count() + $byLegalFurniture->count() + 
                              $this->resultCount,
        ];
    }
}
