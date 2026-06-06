<?php

namespace App\Filament\Pages;

use App\Models\Property;
use App\Services\ClassificationService;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Validate;

class Prediction extends Page
{
    protected static ?string $title = 'Price Prediction';
    protected static ?string $navigationLabel = 'Prediction';
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::Sparkles;

    protected string $view = 'filament.pages.prediction';

    #[Validate('required|numeric|min:1|max:10000')]
    public float $area = 100;

    public float $frontage = 5;
    public float $access_road = 10;
    public int $floors = 3;
    public int $bedrooms = 4;
    public int $bathrooms = 2;
    public string $city = 'Hà Nội';
    public string $legal_status = 'Have certificate';
    public string $furniture_state = 'Basic';

    public ?string $prediction = null;
    public ?float $confidence = null;
    public ?string $method = null;
    public ?float $priceScore = null;
    public array $modelMetrics = [];

    protected ClassificationService $classificationService;

    public function __construct()
    {
        $this->classificationService = new ClassificationService();
    }

    public function mount(): void
    {
        $this->modelMetrics = $this->classificationService->getModelMetrics();
    }

    public function predict(): void
    {
        $this->validate([
            'area' => 'required|numeric|min:1|max:10000',
            'floors' => 'nullable|integer|min:1|max:100',
            'bedrooms' => 'nullable|integer|min:0|max:50',
            'bathrooms' => 'nullable|integer|min:0|max:50',
        ]);

        $features = [
            'area' => $this->area,
            'frontage' => $this->frontage,
            'access_road' => $this->access_road,
            'floors' => $this->floors,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'city' => $this->city,
            'legal_status' => $this->legal_status,
            'furniture_state' => $this->furniture_state,
        ];

        $result = $this->classificationService->predict($features);

        $this->prediction = $result['segment'];
        $this->confidence = $result['confidence'];
        $this->method = $result['method'];
        $this->priceScore = $result['price_score'] ?? null;
    }

    public function getAvailableCities(): array
    {
        return Property::select('city')
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(20)
            ->get()
            ->pluck('city')
            ->toArray();
    }

    public function getModelInfo(): array
    {
        return $this->classificationService->getModelInfo();
    }

    public function getSegmentColorClass(): string
    {
        return match($this->prediction) {
            'Low Price' => 'green',
            'Medium Price' => 'amber',
            'High Price' => 'red',
            default => 'gray',
        };
    }

    public function getSegmentIcon(): string
    {
        return match($this->prediction) {
            'Low Price' => 'arrow-down',
            'Medium Price' => 'minus',
            'High Price' => 'arrow-up',
            default => 'question-mark-circle',
        };
    }

    public function getSegmentLabel(): string
    {
        return match($this->prediction) {
            'Low Price' => 'Phân khúc giá thấp',
            'Medium Price' => 'Phân khúc giá trung bình',
            'High Price' => 'Phân khúc giá cao',
            default => 'Chưa xác định',
        };
    }

    public function getSegmentDescription(): string
    {
        return match($this->prediction) {
            'Low Price' => 'Bất động sản này có giá thuộc phân khúc thấp. Phù hợp với ngân sách hạn chế hoặc đầu tư cho thuê.',
            'Medium Price' => 'Bất động sản này có giá trung bình. Đây là lựa chọn cân bằng giữa giá cao và chất lượng.',
            'High Price' => 'Bất động sản này thuộc phân khúc cao cấp. Có thể có vị trí đắc địa, nội thất đầy đủ hoặc diện tích lớn.',
            default => 'Vui lòng nhập thông tin bất động sản để dự đoán phân khúc giá.',
        };
    }
}
