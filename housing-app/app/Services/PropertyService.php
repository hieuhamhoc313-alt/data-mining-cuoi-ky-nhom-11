<?php

namespace App\Services;

use App\Repositories\PropertyRepositoryInterface;

class PropertyService
{
    protected PropertyRepositoryInterface $repository;

    public function __construct(PropertyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function getById(int $id)
    {
        return $this->repository->getById($id);
    }

    public function getStatistics(): array
    {
        return $this->repository->getStatistics();
    }

    public function getRecentProperties(int $limit = 10)
    {
        return $this->repository->getRecent($limit);
    }

    public function getByFilters(array $filters)
    {
        return $this->repository->getByFilters($filters);
    }

    public function paginate(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function create(array $data)
    {
        if (!isset($data['price_segment'])) {
            $data['price_segment'] = $this->calculatePriceSegment($data['price']);
        }

        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        if (isset($data['price']) && !isset($data['price_segment'])) {
            $data['price_segment'] = $this->calculatePriceSegment($data['price']);
        }

        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }

    protected function calculatePriceSegment(float $price): string
    {
        $percentiles = $this->getPricePercentiles();
        
        if ($price < $percentiles['low']) {
            return 'Low';
        } elseif ($price < $percentiles['high']) {
            return 'Medium';
        }
        
        return 'High';
    }

    protected function getPricePercentiles(): array
    {
        $prices = $this->repository->getAll()->pluck('price')->toArray();
        
        sort($prices);
        $count = count($prices);
        
        return [
            'low' => $prices[(int) floor($count * 0.33)],
            'high' => $prices[(int) floor($count * 0.67)],
        ];
    }
}
