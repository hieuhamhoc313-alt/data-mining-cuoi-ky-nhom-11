<?php

namespace App\Http\Controllers;

use App\Services\PropertyService;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected PropertyService $propertyService;
    protected AnalyticsService $analyticsService;

    public function __construct(PropertyService $propertyService, AnalyticsService $analyticsService)
    {
        $this->propertyService = $propertyService;
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        $stats = $this->analyticsService->getDashboardStats();
        $recentProperties = $this->propertyService->getRecentProperties(10);
        $priceDistribution = $this->analyticsService->getPriceDistribution();
        $cityDistribution = $this->analyticsService->getCityDistribution();
        $legalDistribution = $this->analyticsService->getLegalStatusDistribution();

        return view('dashboard.index', compact(
            'stats',
            'recentProperties',
            'priceDistribution',
            'cityDistribution',
            'legalDistribution'
        ));
    }
}
