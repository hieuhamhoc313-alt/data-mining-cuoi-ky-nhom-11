<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';

    protected $fillable = [
        'address',
        'city',
        'district',
        'ward',
        'area',
        'frontage',
        'access_road',
        'house_direction',
        'balcony_direction',
        'floors',
        'bedrooms',
        'bathrooms',
        'legal_status',
        'furniture_state',
        'price',
        'price_segment',
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'frontage' => 'decimal:2',
        'access_road' => 'decimal:2',
        'floors' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'price' => 'decimal:2',
    ];

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByLegalStatus($query, $status)
    {
        return $query->where('legal_status', $status);
    }

    public function scopeByFurnitureState($query, $state)
    {
        return $query->where('furniture_state', $state);
    }

    public function scopeByPriceSegment($query, $segment)
    {
        return $query->where('price_segment', $segment);
    }

    public function scopeByAreaRange($query, $min, $max)
    {
        return $query->whereBetween('area', [$min, $max]);
    }

    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function scopeByBedrooms($query, $count)
    {
        return $query->where('bedrooms', $count);
    }

    public function scopeByFloors($query, $count)
    {
        return $query->where('floors', $count);
    }

    public function scopeOrderByPriceDesc($query)
    {
        return $query->orderBy('price', 'desc');
    }

    public function scopeOrderByPriceAsc($query)
    {
        return $query->orderBy('price', 'asc');
    }

    public function scopeOrderByAreaDesc($query)
    {
        return $query->orderBy('area', 'desc');
    }

    public function scopeOrderByCreatedDesc($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
