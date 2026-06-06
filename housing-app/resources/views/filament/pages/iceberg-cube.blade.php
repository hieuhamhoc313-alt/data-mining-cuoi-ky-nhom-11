<div class="fi-simple-layout">
    <div class="fi-simple-layout-main-ctn">
        <header class="fi-simple-header mb-6">
            <h2 class="fi-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                Iceberg Cube Analysis
            </h2>
            <p class="fi-simple-header-subheading mt-1 text-sm text-gray-500 dark:text-gray-400">
                Phân tích đa chiều với GROUP BY CUBE - Lọc nhóm có COUNT(*) > 20
            </p>
        </header>

        {{-- Explanation Banner --}}
        <div class="fi-card relative rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-950/30 dark:to-orange-950/30 p-6 shadow-sm ring-1 ring-amber-200 dark:ring-amber-800/30 mb-6">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-400/20">
                    <x-heroicon-s-information-circle class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-amber-900 dark:text-amber-100">Iceberg Cube hoạt động như thế nào?</h4>
                    <p class="mt-1 text-sm text-amber-700 dark:text-amber-300 leading-relaxed">
                        <strong>GROUP BY CUBE</strong> sinh ra tất cả tổ hợp có thể của các chiều (City, Legal Status, Furniture State).
                        Điều kiện <strong>Iceberg</strong> <code class="px-1 py-0.5 bg-amber-200/50 dark:bg-amber-800/50 rounded text-xs">HAVING COUNT(*) > 20</code>
                        lọc bỏ các nhóm thưa (sparse groups), chỉ giữ lại các mẫu có ý nghĩa thống kê.
                        Với 3 chiều → 2³ = 8 tổ hợp.
                    </p>
                </div>
            </div>
        </div>

        {{-- Cube Stats Cards --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 xl:grid-cols-6 mb-6">
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 mx-auto mb-2 dark:bg-amber-400/10">
                    <x-heroicon-s-cube class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ $cubeStats['total_dimensions'] ?? 3 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Dimensions</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100 mx-auto mb-2 dark:bg-green-400/10">
                    <x-heroicon-s-view-columns class="h-4 w-4 text-green-600 dark:text-green-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ $cubeStats['total_combinations'] ?? 8 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Combinations (2³)</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-purple-100 mx-auto mb-2 dark:bg-purple-400/10">
                    <x-heroicon-s-funnel class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">> {{ $cubeStats['iceberg_threshold'] ?? 20 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Iceberg Threshold</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 mx-auto mb-2 dark:bg-blue-400/10">
                    <x-heroicon-s-chart-bar class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ $cubeStats['total_groups'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Total Groups</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-cyan-100 mx-auto mb-2 dark:bg-cyan-400/10">
                    <x-heroicon-s-document-duplicate class="h-4 w-4 text-cyan-600 dark:text-cyan-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ $cubeStats['level_2'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">2-D Combos</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-pink-100 mx-auto mb-2 dark:bg-pink-400/10">
                    <x-heroicon-s-queue-list class="h-4 w-4 text-pink-600 dark:text-pink-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ $resultCount }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">3-D Groups</p>
            </div>
        </div>

        {{-- Aggregation Levels --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Cube Structure --}}
            <div class="fi-card relative rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-400/10">
                        <x-heroicon-s-cube class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Cấu trúc Cube</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Các tầng aggregation</p>
                    </div>
                </div>

                <div class="flex flex-col items-center space-y-3">
                    {{-- Level 3: Full Cube --}}
                    <div class="w-full flex justify-center">
                        <div class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-lg font-semibold text-sm shadow-lg flex items-center gap-2">
                            <x-heroicon-s-cube class="h-4 w-4" />
                            City × Legal × Furniture ({{ $cubeStats['level_3'] ?? 0 }} nhóm)
                        </div>
                    </div>
                    
                    {{-- Connector --}}
                    <div class="flex items-center gap-2 text-gray-400">
                        <div class="w-px h-6 bg-gray-300 dark:bg-gray-600"></div>
                        <span class="text-xs">2 chiều</span>
                        <div class="w-px h-6 bg-gray-300 dark:bg-gray-600"></div>
                    </div>

                    {{-- Level 2: Pairs --}}
                    <div class="flex flex-wrap justify-center gap-2">
                        <div class="px-3 py-1.5 bg-blue-500 text-white rounded text-xs font-medium">
                            City × Legal ({{ $aggregationLevels['city_legal'] ?? 0 }})
                        </div>
                        <div class="px-3 py-1.5 bg-purple-500 text-white rounded text-xs font-medium">
                            City × Furniture ({{ $aggregationLevels['city_furniture'] ?? 0 }})
                        </div>
                        <div class="px-3 py-1.5 bg-pink-500 text-white rounded text-xs font-medium">
                            Legal × Furniture ({{ $aggregationLevels['legal_furniture'] ?? 0 }})
                        </div>
                    </div>

                    {{-- Connector --}}
                    <div class="flex items-center gap-2 text-gray-400">
                        <div class="w-px h-6 bg-gray-300 dark:bg-gray-600"></div>
                        <span class="text-xs">1 chiều</span>
                        <div class="w-px h-6 bg-gray-300 dark:bg-gray-600"></div>
                    </div>

                    {{-- Level 1: Single --}}
                    <div class="flex flex-wrap justify-center gap-2">
                        <div class="px-3 py-1.5 bg-amber-600 text-white rounded text-xs">
                            City ({{ $aggregationLevels['city'] ?? 0 }})
                        </div>
                        <div class="px-3 py-1.5 bg-green-600 text-white rounded text-xs">
                            Legal ({{ $aggregationLevels['legal'] ?? 0 }})
                        </div>
                        <div class="px-3 py-1.5 bg-cyan-600 text-white rounded text-xs">
                            Furniture ({{ $aggregationLevels['furniture'] ?? 0 }})
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Combinations --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-400/10">
                        <x-heroicon-s-trophy class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Top Combinations</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Nhóm có nhiều BĐS nhất</p>
                    </div>
                </div>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach($topCombinations as $index => $combo)
                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                            <div class="flex items-center gap-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-700 dark:bg-amber-400/10 dark:text-amber-400">{{ $index + 1 }}</span>
                                <div class="text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs dark:bg-blue-400/10 dark:text-blue-400">{{ $combo['city'] ?? 'N/A' }}</span>
                                    <span class="mx-1 text-gray-400">×</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-100 text-purple-800 text-xs dark:bg-purple-400/10 dark:text-purple-400">{{ $combo['legal_status'] ?? 'N/A' }}</span>
                                    <span class="mx-1 text-gray-400">×</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-pink-100 text-pink-800 text-xs dark:bg-pink-400/10 dark:text-pink-400">{{ $combo['furniture_state'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $combo['count'] }}</span>
                                <span class="text-xs text-gray-500 ml-1">BĐS</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Results Table --}}
        <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mb-6">
            <div class="flex items-center gap-x-3 mb-5">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-400/10">
                    <x-heroicon-s-table-cells class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">Kết quả Iceberg Cube</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">City × Legal Status × Furniture State — chỉ nhóm có COUNT > 20</p>
                </div>
                <span class="ml-auto inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-400/10 dark:text-amber-400">
                    {{ $resultCount }} nhóm
                </span>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/80">
                            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Thành phố</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Pháp lý</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nội thất</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Số lượng</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Giá TB</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Giá Min</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Giá Max</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($icebergData as $cityKey => $cityData)
                            @foreach($cityData as $legalKey => $legalData)
                                @foreach($legalData as $furnitureKey => $furnitureData)
                                    <tr class="hover:bg-amber-50/50 dark:hover:bg-amber-900/10 transition-colors">
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-400/10 dark:text-blue-400 border border-blue-200 dark:border-blue-800/30">{{ $cityKey }}</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-400/10 dark:text-purple-400">{{ $legalKey }}</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-pink-100 text-pink-800 dark:bg-pink-400/10 dark:text-pink-400">{{ $furnitureKey }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900">{{ $furnitureData['count'] }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-right font-semibold text-success-600 dark:text-success-400">{{ number_format($furnitureData['avg_price'] / 1e9, 2) }} Tỷ</td>
                                        <td class="py-3 px-4 text-right text-gray-500">{{ number_format($furnitureData['min_price'] / 1e6, 0) }} Tr</td>
                                        <td class="py-3 px-4 text-right text-gray-500">{{ number_format($furnitureData['max_price'] / 1e9, 2) }} Tỷ</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-2">
                                        <x-heroicon-s-inbox class="h-12 w-12 text-gray-300 dark:text-gray-600" />
                                        <p class="text-gray-500 dark:text-gray-400">Chưa có dữ liệu. Vui lòng kiểm tra dữ liệu trong hệ thống.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SQL Query --}}
        <div class="fi-card relative rounded-xl bg-gray-900 p-6 shadow-sm ring-1 ring-gray-700 overflow-hidden">
            <div class="flex items-center gap-x-3 mb-4">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10">
                    <x-heroicon-s-code class="h-4 w-4 text-white/70" />
                </div>
                <h3 class="font-semibold text-white">SQL Query - GROUP BY CUBE</h3>
            </div>
            <pre class="text-sm text-green-400 overflow-x-auto leading-relaxed"><code>-- Iceberg Cube Query với 3 chiều
SELECT
    city,
    legal_status,
    furniture_state,
    COUNT(*)           AS property_count,
    AVG(price)         AS avg_price,
    MIN(price)         AS min_price,
    MAX(price)         AS max_price,
    AVG(area)          AS avg_area
FROM properties
WHERE city IS NOT NULL
  AND legal_status IS NOT NULL
  AND furniture_state IS NOT NULL
GROUP BY CUBE (city, legal_status, furniture_state)
HAVING COUNT(*) > 20
ORDER BY city, legal_status, furniture_state;

-- Giải thích:
-- GROUP BY CUBE tạo 2^3 = 8 tổ hợp:
-- 1. (city, legal, furniture) - đầy đủ 3chiều
-- 2. (city, legal)            - 2chiều
-- 3. (city, furniture)        - 2chiều
-- 4. (legal, furniture)       - 2chiều
-- 5. (city)                   - 1chiều
-- 6. (legal)                  - 1chiều
-- 7. (furniture)             - 1chiều
-- 8. ()                       - grand total</code></pre>
        </div>
    </div>
</div>