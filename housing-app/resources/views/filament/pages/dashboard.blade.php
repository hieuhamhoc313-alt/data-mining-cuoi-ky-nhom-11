<div class="fi-simple-layout">
    <div class="fi-simple-layout-main-ctn">
        <header class="fi-simple-header mb-6">
            <h2 class="fi-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                Tổng quan thị trường BĐS
            </h2>
            <p class="fi-simple-header-subheading mt-1 text-sm text-gray-500 dark:text-gray-400">
                Thống kê tổng quan về dữ liệu bất động sản Việt Nam
            </p>
        </header>

        {{-- Stats Cards Row --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-6">
            {{-- Total Properties --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tổng số BĐS</p>
                        <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ number_format($totalProperties) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-400/10">
                        <x-heroicon-s-building-office-2 class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                    </div>
                </div>
            </div>

            {{-- Average Price --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Giá trung bình</p>
                        <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ number_format($avgPrice / 1e9, 2) }} <span class="text-lg font-medium">tỷ</span></p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-400/10">
                        <x-heroicon-s-currency-dollar class="h-6 w-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            {{-- Max Price --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Giá cao nhất</p>
                        <p class="mt-2 text-3xl font-bold text-success-600 dark:text-success-400">{{ number_format($maxPrice / 1e9, 2) }} <span class="text-lg font-medium">tỷ</span></p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-400/10">
                        <x-heroicon-s-arrow-trending-up class="h-6 w-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            {{-- Min Price --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Giá thấp nhất</p>
                        <p class="mt-2 text-3xl font-bold text-danger-600 dark:text-danger-400">{{ number_format($minPrice / 1e6, 0) }} <span class="text-lg font-medium">triệu</span></p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-400/10">
                        <x-heroicon-s-arrow-trending-down class="h-6 w-6 text-red-600 dark:text-red-400" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Price Distribution + City Distribution Row --}}
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 mb-6">
            {{-- Price Distribution --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-400/10">
                        <x-heroicon-s-chart-pie class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Phân bố phân khúc giá</h3>
                </div>
                <div class="space-y-4">
                    @foreach(['Low' => ['label' => 'Low Price (Thấp)', 'color' => 'success', 'bg' => 'bg-success-500'], 'Medium' => ['label' => 'Medium Price (Trung bình)', 'color' => 'warning', 'bg' => 'bg-warning-500'], 'High' => ['label' => 'High Price (Cao)', 'color' => 'danger', 'bg' => 'bg-danger-500']] as $key => $segment)
                        @php $count = $priceDistribution[$key] ?? 0; $percent = $totalProperties > 0 ? max(1, ($count / $totalProperties) * 100) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $segment['label'] }}</span>
                                <span class="text-sm font-bold text-{{ $segment['color'] }}-600 dark:text-{{ $segment['color'] }}-400">{{ $count }} BĐS</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="{{ $segment['bg'] }} h-2 rounded-full transition-all" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- City Distribution --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-400/10">
                        <x-heroicon-s-map-pin class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">BĐS theo thành phố</h3>
                </div>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @forelse($cityDistribution as $city => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $city }}</span>
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-400/10 dark:text-amber-400">{{ $count }}</span>
                        </div>
                    @empty
                        <p class="text-center text-sm text-gray-400 py-4">Chưa có dữ liệu</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Legal + Quick Stats Row --}}
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 mb-6">
            {{-- Legal Status --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-400/10">
                        <x-heroicon-s-document-check class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Tình trạng pháp lý</h3>
                </div>
                <div class="space-y-3">
                    @forelse($legalDistribution as $status => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $status ?? 'N/A' }}</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($count) }}</span>
                        </div>
                    @empty
                        <p class="text-center text-sm text-gray-400 py-4">Chưa có dữ liệu</p>
                    @endforelse
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-100 dark:bg-cyan-400/10">
                        <x-heroicon-s-chart-bar-square class="h-4 w-4 text-cyan-600 dark:text-cyan-400" />
                    </div>
                    <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Thống kê nhanh</h3>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-calculator class="h-4 w-4 text-gray-400" />
                            <span class="text-sm text-gray-600 dark:text-gray-400">Diện tích TB</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($avgArea, 1) }} m²</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-banknotes class="h-4 w-4 text-gray-400" />
                            <span class="text-sm text-gray-600 dark:text-gray-400">Tổng giá trị ước tính</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalValue, 2) }} Tỷ</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-currency-dollar class="h-4 w-4 text-gray-400" />
                            <span class="text-sm text-gray-600 dark:text-gray-400">Giá trung bình</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($avgPrice / 1e9, 2) }} Tỷ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
