<?php

namespace App\Http\Controllers;

use App\Http\Requests\PredictionRequest;
use App\Services\ClassificationService;
use Illuminate\Http\JsonResponse;
use Throwable;

class PredictionController extends Controller
{
    protected ClassificationService $classificationService;

    public function __construct(ClassificationService $classificationService)
    {
        $this->classificationService = $classificationService;
    }

    public function index()
    {
        $modelInfo = $this->classificationService->getModelInfo();

        return view('predict.index', compact('modelInfo'));
    }

    public function predict(PredictionRequest $request): JsonResponse
    {
        try {
            $features = $request->validated();
            $result = $this->classificationService->predict($features);

            return response()->json([
                'success' => true,
                'prediction' => $result,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Không thể xử lý dự đoán lúc này. Vui lòng kiểm tra cấu hình mô hình hoặc dữ liệu hệ thống.',
            ], 500);
        }
    }
}
