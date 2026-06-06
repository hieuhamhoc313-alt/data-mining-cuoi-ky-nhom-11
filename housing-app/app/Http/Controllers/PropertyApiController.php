<?php

namespace App\Http\Controllers;

use App\Services\ClassificationService;
use App\Services\PropertyService;
use App\Services\AnalyticsService;
use App\Http\Requests\PredictionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertyApiController extends Controller
{
    protected PropertyService $propertyService;
    protected AnalyticsService $analyticsService;
    protected ClassificationService $classificationService;

    public function __construct(
        PropertyService $propertyService,
        AnalyticsService $analyticsService,
        ClassificationService $classificationService
    ) {
        $this->propertyService = $propertyService;
        $this->analyticsService = $analyticsService;
        $this->classificationService = $classificationService;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $properties = $this->propertyService->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $properties->items(),
            'meta' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $property = $this->propertyService->getById($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $property,
        ]);
    }

    public function analytics(Request $request): JsonResponse
    {
        $type = $request->input('type', 'city');

        $data = match($type) {
            'city' => $this->analyticsService->getPriceByCity(),
            'legal' => $this->analyticsService->getPriceByLegalStatus(),
            'furniture' => $this->analyticsService->getPriceByFurnitureState(),
            'bedrooms' => $this->analyticsService->getPriceByBedrooms(),
            'area' => $this->analyticsService->getPriceByArea(),
            default => $this->analyticsService->getDashboardStats(),
        };

        return response()->json([
            'success' => true,
            'type' => $type,
            'data' => $data,
        ]);
    }

    public function statistics(): JsonResponse
    {
        $stats = $this->analyticsService->getDashboardStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Predict price segment from features
     */
    public function predict(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'area' => 'required|numeric|min:1|max:10000',
            'frontage' => 'nullable|numeric|min:0|max:1000',
            'access_road' => 'nullable|numeric|min:0|max:1000',
            'floors' => 'nullable|integer|min:1|max:100',
            'bedrooms' => 'nullable|integer|min:0|max:50',
            'bathrooms' => 'nullable|integer|min:0|max:50',
            'city' => 'nullable|string|max:100',
            'legal_status' => 'nullable|string|in:Have certificate,Sale contract,Pending,Other',
            'furniture_state' => 'nullable|string|in:Full,Basic,Empty',
        ]);

        $features = [
            'area' => (float) ($validated['area'] ?? 100),
            'frontage' => (float) ($validated['frontage'] ?? 0),
            'access_road' => (float) ($validated['access_road'] ?? 0),
            'floors' => (int) ($validated['floors'] ?? 1),
            'bedrooms' => (int) ($validated['bedrooms'] ?? 1),
            'bathrooms' => (int) ($validated['bathrooms'] ?? 1),
            'city' => $validated['city'] ?? 'Other',
            'legal_status' => $validated['legal_status'] ?? 'Have certificate',
            'furniture_state' => $validated['furniture_state'] ?? 'Basic',
        ];

        $result = $this->classificationService->predict($features);

        return response()->json([
            'success' => true,
            'prediction' => [
                'segment' => $result['segment'],
                'confidence' => round($result['confidence'], 4),
                'price_score' => $result['price_score'] ?? null,
                'method' => $result['method'] ?? 'unknown',
            ],
            'features' => $features,
        ]);
    }

    /**
     * Get model information
     */
    public function modelInfo(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'model' => $this->classificationService->getModelInfo(),
            'metrics' => $this->classificationService->getModelMetrics(),
        ]);
    }

    /**
     * Get warehouse summary
     */
    public function warehouse(Request $request): JsonResponse
    {
        $type = $request->input('type', 'summary');

        $data = match($type) {
            'fact' => $this->analyticsService->getDashboardStats(),
            'location' => $this->analyticsService->getPriceByCity(),
            'legal' => $this->analyticsService->getPriceByLegalStatus(),
            'furniture' => $this->analyticsService->getPriceByFurnitureState(),
            default => $this->analyticsService->getDashboardStats(),
        };

        return response()->json([
            'success' => true,
            'type' => $type,
            'data' => $data,
        ]);
    }
}
