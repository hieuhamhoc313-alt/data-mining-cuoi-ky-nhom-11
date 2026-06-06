@extends('layouts.app')

@section('title', 'Analytics - Vietnam Real Estate Analytics')
@section('page-title', 'Analytics')

@section('content')
<div class="container-fluid">
    {{-- Analytics Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-4 text-primary mb-2">
                        <i class="bi bi-bar-chart"></i>
                    </div>
                    <h5>Giá theo thành phố</h5>
                    <p class="text-muted small">Xem giá trung bình theo thành phố</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-4 text-success mb-2">
                        <i class="bi bi-bar-chart-steps"></i>
                    </div>
                    <h5>Giá theo phòng ngủ</h5>
                    <p class="text-muted small">Thống kê giá theo số phòng ngủ</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-4 text-warning mb-2">
                        <i class="bi bi-file-earmark-check"></i>
                    </div>
                    <h5>Giá theo pháp lý</h5>
                    <p class="text-muted small">Tình trạng pháp lý ảnh hưởng giá</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-4 text-info mb-2">
                        <i class="bi bi-lamp"></i>
                    </div>
                    <h5>Giá theo nội thất</h5>
                    <p class="text-muted small">Mức độ nội thất ảnh hưởng giá</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Price by City --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-geo-alt me-2"></i>Giá trung bình theo thành phố
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Thành phố</th>
                                    <th class="text-end">Số lượng</th>
                                    <th class="text-end">Giá TB</th>
                                    <th class="text-end">Giá min</th>
                                    <th class="text-end">Giá max</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($analytics['by_city'] as $city)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $city->city }}</span>
                                    </td>
                                    <td class="text-end">{{ number_format($city->count) }}</td>
                                    <td class="text-end fw-bold">{{ number_format($city->avg_price, 2) }}</td>
                                    <td class="text-end">{{ number_format($city->min_price, 2) }}</td>
                                    <td class="text-end">{{ number_format($city->max_price, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Chưa có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Biểu đồ giá theo thành phố
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="priceByCityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Price by Legal Status --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-check me-2"></i>Giá theo tình trạng pháp lý
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tình trạng pháp lý</th>
                                    <th class="text-end">Số lượng</th>
                                    <th class="text-end">Giá TB</th>
                                    <th class="text-end">Giá min</th>
                                    <th class="text-end">Giá max</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($analytics['by_legal_status'] as $legal)
                                <tr>
                                    <td>
                                        <span class="badge bg-success">{{ $legal->legal_status ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-end">{{ number_format($legal->count) }}</td>
                                    <td class="text-end fw-bold">{{ number_format($legal->avg_price, 2) }}</td>
                                    <td class="text-end">{{ number_format($legal->min_price, 2) }}</td>
                                    <td class="text-end">{{ number_format($legal->max_price, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Chưa có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i>Biểu đồ pháp lý
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="priceByLegalChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Price by Furniture State --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lamp me-2"></i>Giá theo tình trạng nội thất
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tình trạng nội thất</th>
                                    <th class="text-end">Số lượng</th>
                                    <th class="text-end">Giá TB</th>
                                    <th class="text-end">Giá min</th>
                                    <th class="text-end">Giá max</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($analytics['by_furniture_state'] as $furniture)
                                <tr>
                                    <td>
                                        <span class="badge bg-warning">{{ $furniture->furniture_state ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-end">{{ number_format($furniture->count) }}</td>
                                    <td class="text-end fw-bold">{{ number_format($furniture->avg_price, 2) }}</td>
                                    <td class="text-end">{{ number_format($furniture->min_price, 2) }}</td>
                                    <td class="text-end">{{ number_format($furniture->max_price, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Chưa có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart-horizontal me-2"></i>Biểu đồ nội thất
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="priceByFurnitureChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Price by Bedrooms --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-house me-2"></i>Giá theo số phòng ngủ
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Số phòng ngủ</th>
                                    <th class="text-end">Số lượng</th>
                                    <th class="text-end">Giá TB</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($analytics['by_bedrooms'] as $bedroom)
                                <tr>
                                    <td>
                                        <span class="badge bg-info">{{ $bedroom->bedrooms ?? 'N/A' }} phòng</span>
                                    </td>
                                    <td class="text-end">{{ number_format($bedroom->count) }}</td>
                                    <td class="text-end fw-bold">{{ number_format($bedroom->avg_price, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Chưa có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart-steps me-2"></i>Biểu đồ phòng ngủ
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="priceByBedroomsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const analyticsData = {
        byCity: @json($analytics['by_city']),
        byLegal: @json($analytics['by_legal_status']),
        byFurniture: @json($analytics['by_furniture_state']),
        byBedrooms: @json($analytics['by_bedrooms'])
    };

    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";

    // Price by City Chart
    new Chart(document.getElementById('priceByCityChart'), {
        type: 'bar',
        data: {
            labels: analyticsData.byCity.map(d => d.city),
            datasets: [{
                label: 'Giá trung bình (Tỷ đồng)',
                data: analyticsData.byCity.map(d => d.avg_price),
                backgroundColor: '#3498db',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: false }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Price by Legal Chart
    new Chart(document.getElementById('priceByLegalChart'), {
        type: 'doughnut',
        data: {
            labels: analyticsData.byLegal.map(d => d.legal_status || 'N/A'),
            datasets: [{
                data: analyticsData.byLegal.map(d => d.count),
                backgroundColor: ['#2ecc71', '#3498db', '#f39c12', '#9b59b6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Price by Furniture Chart
    new Chart(document.getElementById('priceByFurnitureChart'), {
        type: 'bar',
        data: {
            labels: analyticsData.byFurniture.map(d => d.furniture_state || 'N/A'),
            datasets: [{
                label: 'Giá trung bình',
                data: analyticsData.byFurniture.map(d => d.avg_price),
                backgroundColor: ['#f39c12', '#e74c3c', '#9b59b6'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });

    // Price by Bedrooms Chart
    new Chart(document.getElementById('priceByBedroomsChart'), {
        type: 'line',
        data: {
            labels: analyticsData.byBedrooms.map(d => d.bedrooms + ' phòng'),
            datasets: [{
                label: 'Giá trung bình',
                data: analyticsData.byBedrooms.map(d => d.avg_price),
                borderColor: '#2ecc71',
                backgroundColor: 'rgba(46, 204, 113, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush
