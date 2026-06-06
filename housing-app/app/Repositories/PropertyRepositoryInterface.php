<?php

namespace App\Repositories;

interface PropertyRepositoryInterface
{
    public function getAll();
    
    public function getById(int $id);
    
    public function getByCity(string $city);
    
    public function getByLegalStatus(string $status);
    
    public function getByFurnitureState(string $state);
    
    public function getByPriceSegment(string $segment);
    
    public function getStatistics();
    
    public function getPriceStatsByCity();
    
    public function getPriceStatsByLegalStatus();
    
    public function getPriceStatsByFurnitureState();
    
    public function getPriceStatsByBedrooms();
    
    public function getPriceStatsByArea();
    
    public function getIcebergCubeData();
    
    public function create(array $data);
    
    public function update(int $id, array $data);
    
    public function delete(int $id);
    
    public function count();
    
    public function paginate(int $perPage = 15);
}
