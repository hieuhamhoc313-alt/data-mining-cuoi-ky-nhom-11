<?php

namespace App\Filament\Pages;

use App\Models\Property;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Warehouse extends Page
{
    protected static ?string $title = 'Data Warehouse';
    protected static ?string $navigationLabel = 'Data Warehouse';
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::CircleStack;

    protected string $view = 'filament.pages.warehouse';

    public array $factStats = [];
    public array $dimLocation = [];
    public array $dimLegal = [];
    public array $dimFurniture = [];

    public function mount(): void
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        $this->factStats = [
            'total_records' => Property::count(),
            'avg_price' => Property::avg('price') ?? 0,
            'max_price' => Property::max('price') ?? 0,
            'min_price' => Property::min('price') ?? 0,
            'avg_area' => Property::avg('area') ?? 0,
            'total_value' => (Property::avg('price') ?? 0) * Property::count() / 1e12,
        ];

        $this->dimLocation = Property::select('city')
            ->selectRaw('COUNT(*) as property_count, AVG(price) as avg_price')
            ->groupBy('city')
            ->orderByDesc('property_count')
            ->limit(10)
            ->get()
            ->toArray();

        $this->dimLegal = Property::select('legal_status')
            ->selectRaw('COUNT(*) as count, AVG(price) as avg_price')
            ->groupBy('legal_status')
            ->orderByDesc('count')
            ->get()
            ->toArray();

        $this->dimFurniture = Property::select('furniture_state')
            ->selectRaw('COUNT(*) as count, AVG(price) as avg_price')
            ->groupBy('furniture_state')
            ->orderByDesc('count')
            ->get()
            ->toArray();
    }
}
