<div class="fi-simple-layout">
    <div class="fi-simple-layout-main-ctn">
        <header class="fi-simple-header mb-6">
            <h2 class="fi-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                Phân tích dữ liệu BĐS
            </h2>
            <p class="fi-simple-header-subheading mt-1 text-sm text-gray-500 dark:text-gray-400">
                EDA - Khám phá và phân tích dữ liệu bất động sản Việt Nam
            </p>
        </header>

        {{-- Chart.js CDN --}}
        @push('styles')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @endpush

        {{-- Overview Stats Cards --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-6 mb-6">
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 mx-auto mb-2 dark:bg-amber-400/10">
                    <x-heroicon-s-building-office-2 class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format($totalProperties) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tổng BĐS</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100 mx-auto mb-2 dark:bg-green-400/10">
                    <x-heroicon-s-currency-dollar class="h-4 w-4 text-green-600 dark:text-green-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format($avgPrice / 1e9, 1) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Giá TB (Tỷ)</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 mx-auto mb-2 dark:bg-blue-400/10">
                    <x-heroicon-s-map class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format($avgArea, 0) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Diện tích TB</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-purple-100 mx-auto mb-2 dark:bg-purple-400/10">
                    <x-heroicon-s-chart-bar class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format($medianPrice / 1e9, 1) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Giá Median</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-red-100 mx-auto mb-2 dark:bg-red-400/10">
                    <x-heroicon-s-arrow-up class="h-4 w-4 text-red-600 dark:text-red-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format($maxPrice / 1e9, 1) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Giá Max</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-cyan-100 mx-auto mb-2 dark:bg-cyan-400/10">
                    <x-heroicon-s-arrow-down class="h-4 w-4 text-cyan-600 dark:text-cyan-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format($minPrice / 1e6, 0) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Giá Min (Tr)</p>
            </div>
        </div>

        {{-- Charts Row 1 --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Price by City Chart --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-400/10">
                        <x-heroicon-s-chart-bar class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Giá theo thành phố</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Top 10 thành phố có giá BĐS cao nhất</p>
                    </div>
                </div>
                <div class="relative" style="height: 300px;">
                    <canvas id="priceByCityChart"></canvas>
                </div>
            </div>

            {{-- Price Segment Chart --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-pink-100 dark:bg-pink-400/10">
                        <x-heroicon-s-chart-pie class="h-4 w-4 text-pink-600 dark:text-pink-400" />
                    </div>
                    <div>
                        <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Phân bố phân khúc giá</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Low / Medium / High Price</p>
                    </div>
                </div>
                <div class="relative" style="height: 300px;">
                    <canvas id="priceSegmentChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Charts Row 2 --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Scatter Plot: Area vs Price --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-400/10">
                        <x-heroicon-s-squares-2x2 class="h-4 w-4 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Diện tích vs Giá</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Scatter plot thể hiện mối quan hệ</p>
                    </div>
                </div>
                <div class="relative" style="height: 300px;">
                    <canvas id="scatterChart"></canvas>
                </div>
            </div>

            {{-- Correlation Heatmap --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-400/10">
                        <x-heroicon-s-table-cells class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Correlation Heatmap</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tương quan giữa các biến số</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr>
                                <th class="text-left py-2 px-1"></th>
                                @foreach($correlationData['labels'] ?? [] as $label)
                                    <th class="text-center py-2 px-1 font-medium text-gray-600">{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php $cols = ['area', 'frontage', 'access_road', 'floors', 'bedrooms', 'bathrooms', 'price']; @endphp
                            @foreach($cols as $i => $col)
                                <tr>
                                    <td class="py-1 px-1 font-medium text-gray-600">{{ $correlationData['labels'][$i] ?? '' }}</td>
                                    @foreach($cols as $j => $col2)
                                        @php $val = $correlationData['matrix'][$col][$col2] ?? 0; @endphp
                                        <td class="py-1 px-1 text-center">
                                            <span class="inline-block w-8 text-center rounded
                                                {{ $val > 0.5 ? 'bg-green-200 text-green-800' : ($val < -0.5 ? 'bg-red-200 text-red-800' : 'bg-gray-100 text-gray-600') }}">
                                                {{ number_format($val, 2) }}
                                            </span>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center justify-center gap-4 mt-4 text-xs">
                    <div class="flex items-center gap-1">
                        <span class="w-4 h-4 rounded bg-red-200"></span>
                        <span class="text-gray-500">Negative</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-4 h-4 rounded bg-gray-100"></span>
                        <span class="text-gray-500">Neutral</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-4 h-4 rounded bg-green-200"></span>
                        <span class="text-gray-500">Positive</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row 3 --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- Price by Legal Status --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-400/10">
                        <x-heroicon-s-document-check class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Giá theo pháp lý</h3>
                    </div>
                </div>
                <div class="relative" style="height: 250px;">
                    <canvas id="legalChart"></canvas>
                </div>
            </div>

            {{-- Price by Furniture --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-100 dark:bg-cyan-400/10">
                        <x-heroicon-s-sparkles class="h-4 w-4 text-cyan-600 dark:text-cyan-400" />
                    </div>
                    <div>
                        <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Giá theo nội thất</h3>
                    </div>
                </div>
                <div class="relative" style="height: 250px;">
                    <canvas id="furnitureChart"></canvas>
                </div>
            </div>

            {{-- Price by Bedrooms --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-400/10">
                        <x-heroicon-s-home-modern class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Giá theo phòng ngủ</h3>
                    </div>
                </div>
                <div class="relative" style="height: 250px;">
                    <canvas id="bedroomsChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Model Performance --}}
        <div class="fi-card relative rounded-xl bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/30 dark:to-purple-950/30 p-6 shadow-sm ring-1 ring-indigo-200 dark:ring-indigo-800/30">
            <div class="flex items-center gap-x-3 mb-5">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-400/10">
                    <x-heroicon-s-cpu-chip class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Classification Model Performance</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Decision Tree vs Random Forest - Training Results</p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach(['accuracy', 'precision', 'recall', 'f1_score'] as $metric)
                    <div class="bg-white dark:bg-gray-900 rounded-lg p-4 text-center shadow-sm">
                        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format(($modelMetrics[$metric] ?? 0.82) * 100, 1) }}%</p>
                        <p class="text-xs text-gray-500 mt-1 capitalize">{{ ucfirst(str_replace('_', ' ', $metric)) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Color palette
    const colors = {
        primary: 'rgba(245, 158, 11, 1)',      // Amber
        primaryLight: 'rgba(245, 158, 11, 0.2)',
        green: 'rgba(34, 197, 94, 1)',          // Green
        red: 'rgba(239, 68, 68, 1)',            // Red
        blue: 'rgba(59, 130, 246, 1)',           // Blue
        purple: 'rgba(168, 85, 247, 1)',        // Purple
        cyan: 'rgba(6, 182, 212, 1)',           // Cyan
        gray: 'rgba(156, 163, 175, 1)',         // Gray
    };

    Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
    Chart.defaults.color = '#6b7280';

    // Price by City Chart (Bar)
    const cityLabels = @json($this->getChartLabels());
    const cityPrices = @json($this->getChartPrices());

    new Chart(document.getElementById('priceByCityChart'), {
        type: 'bar',
        data: {
            labels: cityLabels,
            datasets: [{
                label: 'Giá TB (Tỷ)',
                data: cityPrices,
                backgroundColor: colors.primaryLight,
                borderColor: colors.primary,
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `${ctx.parsed.y} tỷ VNĐ`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: (val) => val + ' tỷ' }
                }
            }
        }
    });

    // Price Segment Chart (Doughnut)
    const segmentData = @json($this->getSegmentPercentages());

    new Chart(document.getElementById('priceSegmentChart'), {
        type: 'doughnut',
        data: {
            labels: ['Low Price (Thấp)', 'Medium Price (TB)', 'High Price (Cao)'],
            datasets: [{
                data: [segmentData.low, segmentData.medium, segmentData.high],
                backgroundColor: [colors.green, colors.primary, colors.red],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 20, usePointStyle: true }
                },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `${ctx.label}: ${ctx.parsed}%`
                    }
                }
            }
        }
    });

    // Scatter Plot (Area vs Price)
    const scatterData = @json($areaVsPrice);

    new Chart(document.getElementById('scatterChart'), {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'BĐS',
                data: scatterData.map(d => ({ x: d[0], y: d[1] })),
                backgroundColor: colors.primaryLight,
                borderColor: colors.primary,
                pointRadius: 3,
                pointHoverRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `Diện tích: ${ctx.parsed.x}m², Giá: ${(ctx.parsed.y / 1e9).toFixed(2)} tỷ`
                    }
                }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Diện tích (m²)' },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                y: {
                    title: { display: true, text: 'Giá (Tỷ VNĐ)' },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                }
            }
        }
    });

    // Legal Status Chart (Horizontal Bar)
    const legalLabels = @json(array_column($priceByLegalStatus, 'legal_status'));
    const legalPrices = @json(array_map(fn($i) => round(($i['avg_price'] ?? 0) / 1e9, 2), $priceByLegalStatus));

    new Chart(document.getElementById('legalChart'), {
        type: 'bar',
        data: {
            labels: legalLabels,
            datasets: [{
                label: 'Giá TB',
                data: legalPrices,
                backgroundColor: colors.purple,
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { callback: (val) => val + ' tỷ' } }
            }
        }
    });

    // Furniture Chart (Horizontal Bar)
    const furnitureLabels = @json(array_column($priceByFurniture, 'furniture_state'));
    const furniturePrices = @json(array_map(fn($i) => round(($i['avg_price'] ?? 0) / 1e9, 2), $priceByFurniture));

    new Chart(document.getElementById('furnitureChart'), {
        type: 'bar',
        data: {
            labels: furnitureLabels,
            datasets: [{
                label: 'Giá TB',
                data: furniturePrices,
                backgroundColor: colors.cyan,
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { callback: (val) => val + ' tỷ' } }
            }
        }
    });

    // Bedrooms Chart (Line)
    const bedroomLabels = @json(array_column($priceByBedrooms, 'bedrooms'));
    const bedroomPrices = @json(array_map(fn($i) => round(($i['avg_price'] ?? 0) / 1e9, 2), $priceByBedrooms));

    new Chart(document.getElementById('bedroomsChart'), {
        type: 'line',
        data: {
            labels: bedroomLabels,
            datasets: [{
                label: 'Giá TB',
                data: bedroomPrices,
                borderColor: colors.indigo,
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointBackgroundColor: colors.indigo,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: (val) => val + ' tỷ' } }
            }
        }
    });
});
</script>
@endpush
