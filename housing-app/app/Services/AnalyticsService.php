<?php

namespace App\Services;

use App\Repositories\PropertyRepositoryInterface;
use Illuminate\Support\Collection;

class AnalyticsService
{
    protected PropertyRepositoryInterface $repository;

    public function __construct(PropertyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getPriceByCity(): Collection
    {
        return $this->repository->getPriceStatsByCity();
    }

    public function getPriceByLegalStatus(): Collection
    {
        return $this->repository->getPriceStatsByLegalStatus();
    }

    public function getPriceByFurnitureState(): Collection
    {
        return $this->repository->getPriceStatsByFurnitureState();
    }

    public function getPriceByBedrooms(): Collection
    {
        return $this->repository->getPriceStatsByBedrooms();
    }

    public function getPriceByArea(): Collection
    {
        return $this->repository->getPriceStatsByArea();
    }

    public function getDashboardStats(): array
    {
        return $this->repository->getStatistics();
    }

    public function getAreaVsPrice(): array
    {
        $properties = $this->repository->getAll()->map(function ($p) {
            return [
                'area' => (float) $p->area,
                'price' => (float) $p->price,
            ];
        });

        return $properties->toArray();
    }

    public function getPriceDistribution(): array
    {
        $properties = $this->repository->getAll();
        
        $segments = $properties->groupBy('price_segment')->map->count();
        
        return [
            'labels' => $segments->keys()->toArray(),
            'data' => $segments->values()->toArray(),
        ];
    }

    public function getLegalStatusDistribution(): array
    {
        $properties = $this->repository->getAll();
        
        $distribution = $properties->groupBy('legal_status')->map->count();
        
        return [
            'labels' => $distribution->keys()->toArray(),
            'data' => $distribution->values()->toArray(),
        ];
    }

    public function getFurnitureDistribution(): array
    {
        $properties = $this->repository->getAll();
        
        $distribution = $properties->groupBy('furniture_state')->map->count();
        
        return [
            'labels' => $distribution->keys()->toArray(),
            'data' => $distribution->values()->toArray(),
        ];
    }

    public function getCityDistribution(): array
    {
        $properties = $this->repository->getAll();
        
        $distribution = $properties->groupBy('city')->map->count()->sortDesc();
        
        return [
            'labels' => $distribution->keys()->take(10)->toArray(),
            'data' => $distribution->values()->take(10)->toArray(),
        ];
    }

    public function getSummaryAnalytics(): array
    {
        return [
            'by_city' => $this->getPriceByCity(),
            'by_legal_status' => $this->getPriceByLegalStatus(),
            'by_furniture_state' => $this->getPriceByFurnitureState(),
            'by_bedrooms' => $this->getPriceByBedrooms(),
            'by_area' => $this->getPriceByArea(),
        ];
    }
}
