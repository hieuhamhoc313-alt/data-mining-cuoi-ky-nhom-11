@extends('layouts.app')

@section('title', 'Dashboard - Vietnam Real Estate Analytics')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    {{-- Statistics Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card h-100">
                <div class="d-flex flex-column justify-content-between h-100">
                    <div>
                        <div class="stat-label">Tổng số BĐS</div>
                        <div class="stat-value">{{ number_format($stats['total_properties'] ?? 0) }}</div>
                    </div>
                    <div class="stat-icon mt-auto">
                        <i class="bi bi-house-door"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card success h-100">
                <div class="d-flex flex-column justify-content-between h-100">
                    <div>
                        <div class="stat-label">Giá trung bình</div>
                        <div class="stat-value">{{ number_format($stats['avg_price'] ?? 0, 2) }}</div>
                        <small>Tỷ đồng</small>
                    </div>
                    <div class="stat-icon mt-auto">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card warning h-100">
                <div class="d-flex flex-column justify-content-between h-100">
                    <div>
                        <div class="stat-label">Giá cao nhất</div>
                        <div class="stat-value">{{ number_format($stats['max_price'] ?? 0, 2) }}</div>
                        <small>Tỷ đồng</small>
                    </div>
                    <div class="stat-icon mt-auto">
                        <i class="bi bi-arrow-up-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card danger h-100">
                <div class="d-flex flex-column justify-content-between h-100">
                    <div>
                        <div class="stat-label">Giá thấp nhất</div>
                        <div class="stat-value">{{ number_format($stats['min_price'] ?? 0, 2) }}</div>
                        <small>Tỷ đồng</small>
                    </div>
                    <div class="stat-icon mt-auto">
                        <i class="bi bi-arrow-down-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i>Phân phối giá
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="priceDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Số lượng BĐS theo thành phố
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="cityDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Second Charts Row --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-check-circle me-2"></i>Tình trạng pháp lý
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="legalDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart-horizontal me-2"></i>Thống kê nhanh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td><i class="bi bi-rulers me-2 text-primary"></i>Diện tích TB</td>
                                    <td class="text-end fw-bold">{{ number_format($stats['avg_area'] ?? 0, 2) }} m<sup>2</sup></td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-cash-stack me-2 text-success"></i>Tổng giá trị</td>
                                    <td class="text-end fw-bold">{{ number_format($stats['total_value'] ?? 0, 2) }} Tỷ</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-graph-up me-2 text-warning"></i>Giá trung bình</td>
                                    <td class="text-end fw-bold">{{ number_format($stats['avg_price'] ?? 0, 2) }} Tỷ</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Properties Table --}}
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Bất động sản mới nhất
            </h5>
            <a href="{{ route('analytics.index') }}" class="btn btn-sm btn-primary">
                Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Địa chỉ</th>
                            <th>Thành phố</th>
                            <th>Diện tích</th>
                            <th>Số tầng</th>
                            <th>Phòng ngủ</th>
                            <th>Trạng thái pháp lý</th>
                            <th>Giá</th>
                            <th>Phân khúc</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentProperties as $property)
                        <tr>
                            <td>
                                <small>{{ Str::limit($property->address, 40) }}</small>
                            </td>
                            <td>{{ $property->city }}</td>
                            <td>{{ $property->area }} m<sup>2</sup></td>
                            <td>{{ $property->floors ?? '-' }}</td>
                            <td>{{ $property->bedrooms ?? '-' }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $property->legal_status ?? 'N/A' }}</span>
                            </td>
                            <td class="fw-bold text-primary">{{ number_format($property->price, 2) }}</td>
                            <td>
                                @if($property->price_segment)
                                    <span class="badge badge-{{ strtolower($property->price_segment) }}">
                                        {{ $property->price_segment }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Chưa có dữ liệu. Vui lòng chạy seeder để nạp dữ liệu.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const priceDistributionData = @json($priceDistribution);
    const cityDistributionData = @json($cityDistribution);
    const legalDistributionData = @json($legalDistribution);

    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    Chart.defaults.color = '#6c757d';

    // Price Distribution Chart
    new Chart(document.getElementById('priceDistributionChart'), {
        type: 'doughnut',
        data: {
            labels: priceDistributionData.labels || ['Low', 'Medium', 'High'],
            datasets: [{
                data: priceDistributionData.data || [1, 1, 1],
                backgroundColor: ['#27ae60', '#f39c12', '#e74c3c'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // City Distribution Chart
    new Chart(document.getElementById('cityDistributionChart'), {
        type: 'bar',
        data: {
            labels: cityDistributionData.labels || [],
            datasets: [{
                label: 'Số lượng BĐS',
                data: cityDistributionData.data || [],
                backgroundColor: '#3498db',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Legal Distribution Chart
    new Chart(document.getElementById('legalDistributionChart'), {
        type: 'pie',
        data: {
            labels: legalDistributionData.labels || [],
            datasets: [{
                data: legalDistributionData.data || [],
                backgroundColor: ['#2ecc71', '#3498db', '#f39c12', '#9b59b6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
</script>
@endpush
