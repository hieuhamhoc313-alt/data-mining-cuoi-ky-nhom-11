<?php

namespace App\Repositories;

use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PropertyRepository implements PropertyRepositoryInterface
{
    protected Property $model;

    public function __construct(Property $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function getById(int $id): ?Property
    {
        return $this->model->find($id);
    }

    public function getByCity(string $city): Collection
    {
        return $this->model->byCity($city)->get();
    }

    public function getByLegalStatus(string $status): Collection
    {
        return $this->model->byLegalStatus($status)->get();
    }

    public function getByFurnitureState(string $state): Collection
    {
        return $this->model->byFurnitureState($state)->get();
    }

    public function getByPriceSegment(string $segment): Collection
    {
        return $this->model->byPriceSegment($segment)->get();
    }

    public function getStatistics(): array
    {
        $stats = $this->model->selectRaw('
            COUNT(*) as total_properties,
            AVG(price) as avg_price,
            MIN(price) as min_price,
            MAX(price) as max_price,
            AVG(area) as avg_area,
            SUM(price) as total_value
        ')->first();

        return [
            'total_properties' => (int) $stats->total_properties,
            'avg_price' => round((float) $stats->avg_price, 2),
            'min_price' => (float) $stats->min_price,
            'max_price' => (float) $stats->max_price,
            'avg_area' => round((float) $stats->avg_area, 2),
            'total_value' => round((float) $stats->total_value, 2),
        ];
    }

    public function getPriceStatsByCity(): Collection
    {
        return $this->model
            ->selectRaw('city, 
                        COUNT(*) as count, 
                        AVG(price) as avg_price, 
                        MIN(price) as min_price, 
                        MAX(price) as max_price')
            ->groupBy('city')
            ->orderByDesc('count')
            ->get();
    }

    public function getPriceStatsByLegalStatus(): Collection
    {
        return $this->model
            ->selectRaw('legal_status, 
                        COUNT(*) as count, 
                        AVG(price) as avg_price, 
                        MIN(price) as min_price, 
                        MAX(price) as max_price')
            ->groupBy('legal_status')
            ->orderByDesc('avg_price')
            ->get();
    }

    public function getPriceStatsByFurnitureState(): Collection
    {
        return $this->model
            ->selectRaw('furniture_state, 
                        COUNT(*) as count, 
                        AVG(price) as avg_price, 
                        MIN(price) as min_price, 
                        MAX(price) as max_price')
            ->groupBy('furniture_state')
            ->orderByDesc('avg_price')
            ->get();
    }

    public function getPriceStatsByBedrooms(): Collection
    {
        return $this->model
            ->selectRaw('bedrooms, 
                        COUNT(*) as count, 
                        AVG(price) as avg_price')
            ->whereNotNull('bedrooms')
            ->groupBy('bedrooms')
            ->orderBy('bedrooms')
            ->get();
    }

    public function getPriceStatsByArea(): Collection
    {
        return $this->model
            ->selectRaw('
                CASE 
                    WHEN area < 50 THEN "Under 50m²"
                    WHEN area >= 50 AND area < 100 THEN "50-100m²"
                    WHEN area >= 100 AND area < 200 THEN "100-200m²"
                    WHEN area >= 200 AND area < 500 THEN "200-500m²"
                    ELSE "Over 500m²"
                END as area_range,
                COUNT(*) as count,
                AVG(price) as avg_price,
                AVG(area) as avg_area
            ')
            ->groupBy('area_range')
            ->orderByRaw('MIN(area)')
            ->get();
    }

    public function getIcebergCubeData(): Collection
    {
        return $this->model
            ->selectRaw('
                city,
                legal_status,
                furniture_state,
                COUNT(*) as property_count,
                AVG(price) as avg_price,
                MIN(price) as min_price,
                MAX(price) as max_price
            ')
            ->groupBy('city', 'legal_status', 'furniture_state')
            ->havingRaw('COUNT(*) > 20')
            ->orderBy('city', 'legal_status', 'furniture_state')
            ->get();
    }

    public function create(array $data): Property
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $record = $this->getById($id);
        if ($record) {
            return $record->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $record = $this->getById($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }

    public function count(): int
    {
        return $this->model->count();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function getRecent(int $limit = 10): Collection
    {
        return $this->model
            ->orderByCreatedDesc()
            ->limit($limit)
            ->get();
    }

    public function getByFilters(array $filters): Collection
    {
        $query = $this->model->query();

        if (isset($filters['city'])) {
            $query->byCity($filters['city']);
        }

        if (isset($filters['legal_status'])) {
            $query->byLegalStatus($filters['legal_status']);
        }

        if (isset($filters['furniture_state'])) {
            $query->byFurnitureState($filters['furniture_state']);
        }

        if (isset($filters['price_segment'])) {
            $query->byPriceSegment($filters['price_segment']);
        }

        if (isset($filters['min_area']) && isset($filters['max_area'])) {
            $query->byAreaRange($filters['min_area'], $filters['max_area']);
        }

        if (isset($filters['min_price']) && isset($filters['max_price'])) {
            $query->byPriceRange($filters['min_price'], $filters['max_price']);
        }

        return $query->get();
    }
}
