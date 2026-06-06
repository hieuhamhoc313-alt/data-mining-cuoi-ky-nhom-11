<div class="fi-simple-layout">
    <div class="fi-simple-layout-main-ctn">
        <header class="fi-simple-header mb-6">
            <h2 class="fi-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                Dự đoán phân khúc giá BĐS
            </h2>
            <p class="fi-simple-header-subheading mt-1 text-sm text-gray-500 dark:text-gray-400">
                Classification Model - Random Forest Classifier | Low / Medium / High Price
            </p>
        </header>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Prediction Form --}}
            <div class="fi-card relative rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 p-6 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-400/10">
                        <x-heroicon-s-sparkles class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Nhập thông tin BĐS</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Điền thông tin bất động sản để dự đoán phân khúc giá</p>
                    </div>
                </div>

                <div class="p-6">
                    <form wire:submit="predict" class="space-y-5">
                        {{-- Area --}}
                        <div>
                            <label for="area" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Diện tích <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    wire:model.live="area"
                                    id="area"
                                    placeholder="VD: 100"
                                    min="1"
                                    max="10000"
                                    step="0.1"
                                    required
                                    class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm shadow-sm transition duration-75 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 disabled:opacity-70 dark:text-gray-200 @error('area') border-red-500 dark:border-red-500 @enderror"
                                />
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">m²</span>
                            </div>
                            @error('area') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Frontage + Access Road --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="frontage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Mặt tiền (m)</label>
                                <input
                                    type="number"
                                    wire:model.live="frontage"
                                    id="frontage"
                                    placeholder="VD: 5"
                                    step="0.1"
                                    min="0"
                                    class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm shadow-sm transition duration-75 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 disabled:opacity-70 dark:text-gray-200"
                                />
                            </div>
                            <div>
                                <label for="access_road" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Đường vào (m)</label>
                                <input
                                    type="number"
                                    wire:model.live="access_road"
                                    id="access_road"
                                    placeholder="VD: 10"
                                    step="0.1"
                                    min="0"
                                    class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm shadow-sm transition duration-75 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 disabled:opacity-70 dark:text-gray-200"
                                />
                            </div>
                        </div>

                        {{-- Floors, Bedrooms, Bathrooms --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label for="floors" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Số tầng</label>
                                <input
                                    type="number"
                                    wire:model.live="floors"
                                    id="floors"
                                    placeholder="VD: 3"
                                    min="1"
                                    class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm shadow-sm transition duration-75 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 disabled:opacity-70 dark:text-gray-200"
                                />
                            </div>
                            <div>
                                <label for="bedrooms" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phòng ngủ</label>
                                <input
                                    type="number"
                                    wire:model.live="bedrooms"
                                    id="bedrooms"
                                    placeholder="VD: 4"
                                    min="0"
                                    class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm shadow-sm transition duration-75 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 disabled:opacity-70 dark:text-gray-200"
                                />
                            </div>
                            <div>
                                <label for="bathrooms" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phòng tắm</label>
                                <input
                                    type="number"
                                    wire:model.live="bathrooms"
                                    id="bathrooms"
                                    placeholder="VD: 3"
                                    min="0"
                                    class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm shadow-sm transition duration-75 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 disabled:opacity-70 dark:text-gray-200"
                                />
                            </div>
                        </div>

                        {{-- Selects --}}
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Thành phố</label>
                            <select
                                wire:model.live="city"
                                id="city"
                                class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm shadow-sm transition duration-75 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 disabled:opacity-70 dark:text-gray-200"
                            >
                                <option value="Hà Nội">Hà Nội</option>
                                <option value="Hồ Chí Minh">Hồ Chí Minh</option>
                                <option value="Đà Nẵng">Đà Nẵng</option>
                                <option value="Hải Phòng">Hải Phòng</option>
                                <option value="Cần Thơ">Cần Thơ</option>
                                <option value="Hưng Yên">Hưng Yên</option>
                                <option value="Bình Dương">Bình Dương</option>
                                <option value="Đồng Nai">Đồng Nai</option>
                                <option value="Quảng Ninh">Quảng Ninh</option>
                                <option value="Other">Khác</option>
                            </select>
                        </div>

                        <div>
                            <label for="legal_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tình trạng pháp lý</label>
                            <select
                                wire:model.live="legal_status"
                                id="legal_status"
                                class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm shadow-sm transition duration-75 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 disabled:opacity-70 dark:text-gray-200"
                            >
                                <option value="Have certificate">Have certificate (Đã có sổ)</option>
                                <option value="Sale contract">Sale contract (Hợp đồng mua bán)</option>
                                <option value="Pending">Pending (Đang chờ)</option>
                                <option value="Other">Other (Khác)</option>
                            </select>
                        </div>

                        <div>
                            <label for="furniture_state" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tình trạng nội thất</label>
                            <select
                                wire:model.live="furniture_state"
                                id="furniture_state"
                                class="fi-input block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm shadow-sm transition duration-75 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 disabled:opacity-70 dark:text-gray-200"
                            >
                                <option value="Full">Full (Đầy đủ)</option>
                                <option value="Basic">Basic (Cơ bản)</option>
                                <option value="Empty">Empty (Trống)</option>
                            </select>
                        </div>

                        {{-- Submit Button --}}
                        <button
                            type="button"
                            wire:click="predict"
                            class="fi-btn relative grid-flow-col items-center justify-center font-semibold rounded-lg transition duration-75 gap-1-5 text-sm inline-grid px-4 py-2.5 bg-amber-500 text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 disabled:opacity-50 w-full mt-2"
                        >
                            <x-heroicon-s-sparkles class="w-4 h-4" />
                            <span>Dự đoán phân khúc giá</span>
                        </button>
                    </form>
                </div>

                {{-- Model Metrics --}}
                <div class="px-6 pb-6">
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
                        <h4 class="font-semibold text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
                            <x-heroicon-s-information-circle class="h-4 w-4" />
                            Thông tin mô hình
                        </h4>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="text-center p-2 bg-white dark:bg-gray-900 rounded-lg">
                                <p class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ number_format(($modelMetrics['accuracy'] ?? 0.82) * 100, 1) }}%</p>
                                <p class="text-xs text-gray-500">Accuracy</p>
                            </div>
                            <div class="text-center p-2 bg-white dark:bg-gray-900 rounded-lg">
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ number_format(($modelMetrics['f1_score'] ?? 0.80) * 100, 1) }}%</p>
                                <p class="text-xs text-gray-500">F1-Score</p>
                            </div>
                        </div>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                <x-heroicon-s-check-circle class="h-4 w-4 text-green-500 shrink-0" />
                                Random Forest Classifier
                            </li>
                            <li class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                <x-heroicon-s-check-circle class="h-4 w-4 text-green-500 shrink-0" />
                                3 phân khúc: Low / Medium / High
                            </li>
                            <li class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                <x-heroicon-s-check-circle class="h-4 w-4 text-green-500 shrink-0" />
                                Features: Area, Floors, Bedrooms...
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Prediction Result --}}
            <div class="fi-card relative rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 p-6 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-400/10">
                        <x-heroicon-s-sparkles class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Kết quả dự đoán</h3>
                </div>

                <div class="p-6">
                    @if($prediction)
                        {{-- Result Display --}}
                        <div class="text-center mb-6">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-2xl flex items-center justify-center
                                {{ $prediction === 'Low Price' ? 'bg-green-100 dark:bg-green-900/30' : ($prediction === 'Medium Price' ? 'bg-amber-100 dark:bg-amber-900/30' : 'bg-red-100 dark:bg-red-900/30') }}">
                                @if($prediction === 'Low Price')
                                    <x-heroicon-s-arrow-down class="w-10 h-10 text-green-600 dark:text-green-400" />
                                @elseif($prediction === 'Medium Price')
                                    <x-heroicon-s-minus class="w-10 h-10 text-amber-600 dark:text-amber-400" />
                                @else
                                    <x-heroicon-s-arrow-up class="w-10 h-10 text-red-600 dark:text-red-400" />
                                @endif
                            </div>

                            <h4 class="text-2xl font-bold
                                {{ $prediction === 'Low Price' ? 'text-green-600 dark:text-green-400' : ($prediction === 'Medium Price' ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                                {{ $prediction }}
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $this->getSegmentLabel() }}
                            </p>
                        </div>

                        {{-- Confidence + Method + Score --}}
                        <div class="grid grid-cols-3 gap-3 mb-5">
                            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4 text-center border border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Confidence</p>
                                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format($confidence * 100, 1) }}%</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4 text-center border border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Phương pháp</p>
                                <p class="text-sm font-bold text-gray-950 dark:text-white">{{ $method === 'rule_based' ? 'Rule-based' : 'ML Model' }}</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4 text-center border border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Score</p>
                                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format($priceScore ?? 0, 1) }}</p>
                            </div>
                        </div>

                        {{-- Analysis --}}
                        <div class="rounded-lg bg-amber-50 dark:bg-amber-950/20 p-4 border border-amber-200 dark:border-amber-800/30">
                            <h5 class="font-semibold text-sm text-amber-900 dark:text-amber-100 mb-2 flex items-center gap-2">
                                <x-heroicon-s-light-bulb class="h-4 w-4" />
                                Phân tích
                            </h5>
                            <p class="text-sm text-amber-800 dark:text-amber-200 leading-relaxed">
                                {{ $this->getSegmentDescription() }}
                            </p>
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-16">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <x-heroicon-s-question-mark-circle class="w-8 h-8 text-gray-300 dark:text-gray-600" />
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">Chưa có kết quả</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Nhập thông tin bất động sản và nhấn "Dự đoán phân khúc giá"</p>
                        </div>
                    @endif
                </div>

                {{-- Segment Descriptions --}}
                @if($prediction)
                <div class="px-6 pb-6">
                    <div class="grid grid-cols-3 gap-3">
                        @foreach([
                            ['key' => 'Low Price', 'label' => 'Low (Thấp)', 'desc' => 'Giá rẻ, ngân sách hạn chế', 'color' => 'green', 'icon' => 'arrow-down', 'bg' => 'bg-green-50 dark:bg-green-950/20 border-green-200 dark:border-green-800/30', 'text' => 'text-green-800 dark:text-green-200'],
                            ['key' => 'Medium Price', 'label' => 'Medium (TB)', 'desc' => 'Cân bằng giá & chất lượng', 'color' => 'amber', 'icon' => 'minus', 'bg' => 'bg-amber-50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800/30', 'text' => 'text-amber-800 dark:text-amber-200'],
                            ['key' => 'High Price', 'label' => 'High (Cao)', 'desc' => 'Cao cấp, đắc địa', 'color' => 'red', 'icon' => 'arrow-up', 'bg' => 'bg-red-50 dark:bg-red-950/20 border-red-200 dark:border-red-800/30', 'text' => 'text-red-800 dark:text-red-200'],
                        ] as $segment)
                            <div class="rounded-lg p-3 border {{ $segment['bg'] }} {{ $prediction === $segment['key'] ? 'ring-2 ring-amber-400' : 'opacity-50' }}">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <x-dynamic-component component="heroicon-s-{{ $segment['icon'] }}" class="h-4 w-4 {{ $segment['text'] }}" />
                                    <span class="text-xs font-semibold {{ $segment['text'] }}">{{ $segment['label'] }}</span>
                                </div>
                                <p class="text-xs {{ $segment['text'] }} opacity-75">{{ $segment['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Segment Legend --}}
        @if(!$prediction)
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach([
                ['label' => 'Low Price (Thấp)', 'desc' => 'Bất động sản giá rẻ, phù hợp với người có ngân sách hạn chế', 'color' => 'green', 'icon' => 'arrow-down', 'bg' => 'bg-green-50 dark:bg-green-950/20 border-green-200 dark:border-green-800/30', 'text' => 'text-green-800 dark:text-green-200'],
                ['label' => 'Medium Price (Trung bình)', 'desc' => 'Bất động sản trung bình, cân bằng giữa giá và chất lượng', 'color' => 'amber', 'icon' => 'minus', 'bg' => 'bg-amber-50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800/30', 'text' => 'text-amber-800 dark:text-amber-200'],
                ['label' => 'High Price (Cao)', 'desc' => 'Bất động sản cao cấp, vị trí đắc địa, nội thất đầy đủ', 'color' => 'red', 'icon' => 'arrow-up', 'bg' => 'bg-red-50 dark:bg-red-950/20 border-red-200 dark:border-red-800/30', 'text' => 'text-red-800 dark:text-red-200'],
            ] as $segment)
                <div class="fi-card relative rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex items-start gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $segment['bg'] }}">
                        <x-dynamic-component component="heroicon-s-{{ $segment['icon'] }}" class="h-4 w-4 {{ $segment['text'] }}" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-sm text-gray-950 dark:text-white">{{ $segment['label'] }}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-relaxed">{{ $segment['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
