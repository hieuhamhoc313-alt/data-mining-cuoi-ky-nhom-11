<div class="fi-simple-layout">
    <div class="fi-simple-layout-main-ctn">
        <header class="fi-simple-header mb-6">
            <h2 class="fi-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                Data Warehouse - Star Schema
            </h2>
            <p class="fi-simple-header-subheading mt-1 text-sm text-gray-500 dark:text-gray-400">
                Thiết kế Star Schema cho phân tích dữ liệu bất động sản Việt Nam
            </p>
        </header>

        {{-- Fact Table Stats Row --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6 mb-6">
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 mx-auto mb-2 dark:bg-amber-400/10">
                    <x-heroicon-s-database class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format($factStats['total_records'] ?? 0) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tổng records</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100 mx-auto mb-2 dark:bg-green-400/10">
                    <x-heroicon-s-currency-dollar class="h-4 w-4 text-green-600 dark:text-green-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format(($factStats['avg_price'] ?? 0) / 1e9, 1) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Giá TB (Tỷ)</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-red-100 mx-auto mb-2 dark:bg-red-400/10">
                    <x-heroicon-s-arrow-up class="h-4 w-4 text-red-600 dark:text-red-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format(($factStats['max_price'] ?? 0) / 1e9, 1) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Giá Max (Tỷ)</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 mx-auto mb-2 dark:bg-blue-400/10">
                    <x-heroicon-s-arrow-down class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format(($factStats['min_price'] ?? 0) / 1e6, 0) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Giá Min (Tr)</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-purple-100 mx-auto mb-2 dark:bg-purple-400/10">
                    <x-heroicon-s-calculator class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format($factStats['avg_area'] ?? 0, 0) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Diện tích TB (m²)</p>
            </div>
            <div class="fi-card relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-cyan-100 mx-auto mb-2 dark:bg-cyan-400/10">
                    <x-heroicon-s-chart-bar class="h-4 w-4 text-cyan-600 dark:text-cyan-400" />
                </div>
                <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format($factStats['total_value'] ?? 0, 1) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tổng giá trị (Tỷ)</p>
            </div>
        </div>

        {{-- Dimension Tables Row --}}
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 mb-6">
            {{-- dim_location --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-400/10">
                        <x-heroicon-s-map-pin class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">dim_location</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Thành phố & quận huyện</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Thành phố</th>
                                <th class="text-right py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Số lượng</th>
                                <th class="text-right py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Giá TB</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($dimLocation as $loc)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="py-2.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-400 border border-amber-100 dark:border-amber-800/30">{{ $loc['city'] ?? 'N/A' }}</span>
                                    </td>
                                    <td class="py-2.5 text-right font-medium">{{ $loc['property_count'] ?? 0 }}</td>
                                    <td class="py-2.5 text-right font-semibold text-success-600 dark:text-success-400">{{ number_format(($loc['avg_price'] ?? 0) / 1e9, 2) }} Tỷ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- dim_legal --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-400/10">
                        <x-heroicon-s-document-check class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">dim_legal</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tình trạng pháp lý</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tình trạng</th>
                                <th class="text-right py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Số lượng</th>
                                <th class="text-right py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Giá TB</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($dimLegal as $legal)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="py-2.5 text-gray-700 dark:text-gray-300">{{ $legal['legal_status'] ?? 'N/A' }}</td>
                                    <td class="py-2.5 text-right font-medium">{{ number_format($legal['count'] ?? 0) }}</td>
                                    <td class="py-2.5 text-right font-semibold text-success-600 dark:text-success-400">{{ number_format(($legal['avg_price'] ?? 0) / 1e9, 2) }} Tỷ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- dim_furniture --}}
            <div class="fi-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-x-3 mb-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-100 dark:bg-cyan-400/10">
                        <x-heroicon-s-sparkles class="h-4 w-4 text-cyan-600 dark:text-cyan-400" />
                    </div>
                    <div>
                        <h3 class="fi-card-header-title font-semibold text-gray-950 dark:text-white">dim_furniture</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tình trạng nội thất</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tình trạng</th>
                                <th class="text-right py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Số lượng</th>
                                <th class="text-right py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Giá TB</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($dimFurniture as $furniture)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="py-2.5 text-gray-700 dark:text-gray-300">{{ $furniture['furniture_state'] ?? 'N/A' }}</td>
                                    <td class="py-2.5 text-right font-medium">{{ number_format($furniture['count'] ?? 0) }}</td>
                                    <td class="py-2.5 text-right font-semibold text-success-600 dark:text-success-400">{{ number_format(($furniture['avg_price'] ?? 0) / 1e9, 2) }} Tỷ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Star Schema Diagram --}}
        <div class="fi-card relative rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 p-8 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="flex items-center gap-x-3 mb-6">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-400/10">
                    <x-heroicon-s-chart-pie class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                </div>
                <h3 class="font-semibold text-gray-950 dark:text-white">Star Schema Diagram</h3>
            </div>

            <div class="flex flex-col items-center space-y-4">
                {{-- Fact Table --}}
                <div class="flex items-center gap-3">
                    <div class="px-6 py-3 bg-amber-500 text-white rounded-xl font-semibold text-sm shadow-lg ring-2 ring-amber-400/50 flex items-center gap-2">
                        <x-heroicon-s-database class="h-4 w-4" />
                        fact_property
                    </div>
                </div>

                {{-- Connector --}}
                <div class="flex flex-col items-center">
                    <div class="w-px h-6 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="w-px h-6 bg-gray-300 dark:bg-gray-600"></div>
                </div>

                {{-- Dimensions Row --}}
                <div class="flex flex-wrap justify-center gap-4">
                    @foreach([
                        ['icon' => 'map-pin', 'color' => 'bg-blue-500 border-blue-400/50 ring-blue-400/50', 'label' => 'dim_location'],
                        ['icon' => 'document-check', 'color' => 'bg-purple-500 border-purple-400/50 ring-purple-400/50', 'label' => 'dim_legal'],
                        ['icon' => 'sparkles', 'color' => 'bg-cyan-500 border-cyan-400/50 ring-cyan-400/50', 'label' => 'dim_furniture'],
                        ['icon' => 'compass', 'color' => 'bg-orange-500 border-orange-400/50 ring-orange-400/50', 'label' => 'dim_direction'],
                        ['icon' => 'calendar', 'color' => 'bg-pink-500 border-pink-400/50 ring-pink-400/50', 'label' => 'dim_date'],
                    ] as $dim)
                        <div class="flex items-center gap-1.5">
                            <div class="flex flex-col items-center">
                                <div class="w-px h-4 bg-gray-300 dark:bg-gray-600"></div>
                            </div>
                            <div class="px-4 py-2 {{ $dim['color'] }} text-white rounded-lg text-xs font-medium shadow flex items-center gap-1.5 border">
                                <x-dynamic-component component="heroicon-s-{{ $dim['icon'] }}" class="h-3 w-3" />
                                {{ $dim['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
