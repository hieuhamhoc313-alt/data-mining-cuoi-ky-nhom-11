<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        $analytics = $this->analyticsService->getSummaryAnalytics();
        $areaVsPrice = $this->analyticsService->getAreaVsPrice();
        $priceDistribution = $this->analyticsService->getPriceDistribution();

        return view('analytics.index', compact(
            'analytics',
            'areaVsPrice',
            'priceDistribution'
        ));
    }

    public function byCity()
    {
        $data = $this->analyticsService->getPriceByCity();
        return response()->json(['data' => $data]);
    }

    public function byLegalStatus()
    {
        $data = $this->analyticsService->getPriceByLegalStatus();
        return response()->json(['data' => $data]);
    }

    public function byFurnitureState()
    {
        $data = $this->analyticsService->getPriceByFurnitureState();
        return response()->json(['data' => $data]);
    }

    public function byBedrooms()
    {
        $data = $this->analyticsService->getPriceByBedrooms();
        return response()->json(['data' => $data]);
    }
}
