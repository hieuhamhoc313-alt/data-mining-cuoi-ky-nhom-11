<?php

namespace App\Filament\Pages;

use App\Models\Property;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class DashboardPage extends Page
{
    protected static ?string $title = 'Dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedHome;

    protected string $view = 'filament.pages.dashboard';

    public int $totalProperties = 0;
    public float $avgPrice = 0;
    public float $maxPrice = 0;
    public float $minPrice = 0;
    public float $avgArea = 0;
    public float $totalValue = 0;

    public array $priceDistribution = [];
    public array $cityDistribution = [];
    public array $legalDistribution = [];

    public function mount(): void
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        $this->totalProperties = Property::count();
        $this->avgPrice = Property::avg('price') ?? 0;
        $this->maxPrice = Property::max('price') ?? 0;
        $this->minPrice = Property::min('price') ?? 0;
        $this->avgArea = Property::avg('area') ?? 0;
        $this->totalValue = ($this->avgPrice * $this->totalProperties) / 1e12;

        $this->priceDistribution = [
            'Low' => Property::where('price_segment', 'Low Price')->count(),
            'Medium' => Property::where('price_segment', 'Medium Price')->count(),
            'High' => Property::where('price_segment', 'High Price')->count(),
        ];

        $this->cityDistribution = Property::select('city')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('city')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->pluck('count', 'city')
            ->toArray();

        $this->legalDistribution = Property::select('legal_status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('legal_status')
            ->get()
            ->pluck('count', 'legal_status')
            ->toArray();
    }
}
