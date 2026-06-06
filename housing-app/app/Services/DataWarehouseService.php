<?php

namespace App\Services;

use App\Repositories\PropertyRepositoryInterface;
use Illuminate\Support\Collection;

class DataWarehouseService
{
    protected PropertyRepositoryInterface $repository;

    public function __construct(PropertyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getFactPropertyStats(): array
    {
        $stats = $this->repository->getStatistics();

        return [
            'total_records' => $stats['total_properties'],
            'avg_price' => $stats['avg_price'],
            'min_price' => $stats['min_price'],
            'max_price' => $stats['max_price'],
            'total_value' => $stats['total_value'],
            'avg_area' => $stats['avg_area'],
        ];
    }

    public function getDimLocation(): Collection
    {
        return $this->repository->getPriceStatsByCity()->map(function ($item) {
            return [
                'city' => $item->city,
                'property_count' => $item->count,
                'avg_price' => round($item->avg_price, 2),
            ];
        });
    }

    public function getDimLegal(): Collection
    {
        return $this->repository->getPriceStatsByLegalStatus();
    }

    public function getDimFurniture(): Collection
    {
        return $this->repository->getPriceStatsByFurnitureState();
    }

    public function getMultiDimensionalAnalysis(): Collection
    {
        $allProperties = $this->repository->getAll();

        return $allProperties
            ->groupBy('city')
            ->map(function ($cityGroup) {
                return $cityGroup->groupBy('legal_status')->map(function ($legalGroup) {
                    return $legalGroup->groupBy('furniture_state')->map(function ($items) {
                        return [
                            'count' => $items->count(),
                            'avg_price' => round($items->avg('price'), 2),
                            'min_price' => round($items->min('price'), 2),
                            'max_price' => round($items->max('price'), 2),
                        ];
                    });
                });
            });
    }

    public function getWarehouseSummary(): array
    {
        return [
            'fact_table' => $this->getFactPropertyStats(),
            'dimension_location' => $this->getDimLocation(),
            'dimension_legal' => $this->getDimLegal(),
            'dimension_furniture' => $this->getDimFurniture(),
        ];
    }
}
