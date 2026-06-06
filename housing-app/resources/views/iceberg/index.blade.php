@extends('layouts.app')

@section('title', 'Iceberg Cube - Vietnam Real Estate Analytics')
@section('page-title', 'Iceberg Cube Analysis')

@section('content')
<div class="container-fluid">
    {{-- Explanation --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="bi bi-info-circle me-2"></i>Iceberg Cube Explanation</h5>
                <p class="mb-0">
                    <strong>GROUP BY CUBE</strong> generates all possible combinations of dimensions (City, Legal Status, Furniture State).
                    The <strong>Iceberg condition</strong> (HAVING COUNT(*) > 20) filters out sparse groups with few records,
                    showing only statistically significant patterns.
                </p>
            </div>
        </div>
    </div>

    {{-- Cube Stats --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="display-5 text-primary mb-2">
                            <i class="bi bi-cube"></i>
                        </div>
                        <h4 class="mb-0">3</h4>
                        <small class="text-muted">Dimensions</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="display-5 text-success mb-2">
                            <i class="bi bi-grid-3x3"></i>
                        </div>
                        <h4 class="mb-0">8</h4>
                        <small class="text-muted">Combinations (2^3)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="display-5 text-warning mb-2">
                            <i class="bi bi-filter"></i>
                        </div>
                        <h4 class="mb-0">> 20</h4>
                        <small class="text-muted">Iceberg Threshold</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="display-5 text-info mb-2">
                            <i class="bi bi-bar-chart"></i>
                        </div>
                        <h4 class="mb-0">{{ $icebergData->count() }}</h4>
                        <small class="text-muted">Result Groups</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Iceberg Cube Results --}}
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-table me-2"></i>Iceberg Cube Results (City x Legal Status x Furniture State)
            </h5>
            <small class="text-muted">Chỉ hiển thị các nhóm có hơn 20 bất động sản</small>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Legal Status</th>
                            <th>Furniture State</th>
                            <th class="text-end">Property Count</th>
                            <th class="text-end">Avg Price</th>
                            <th class="text-end">Min Price</th>
                            <th class="text-end">Max Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($icebergData as $cityKey => $cityData)
                            @foreach($cityData as $legalKey => $legalData)
                                @foreach($legalData as $furnitureKey => $furnitureData)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ $cityKey }}</span>
                                        </td>
                                        <td>{{ $legalKey }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $furnitureKey }}</span>
                                        </td>
                                        <td class="text-end fw-bold">{{ $furnitureData['count'] }}</td>
                                        <td class="text-end">{{ number_format($furnitureData['avg_price'], 2) }}</td>
                                        <td class="text-end">{{ number_format($furnitureData['min_price'], 2) }}</td>
                                        <td class="text-end">{{ number_format($furnitureData['max_price'], 2) }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Chưa có dữ liệu. Vui lòng kiểm tra dữ liệu trong hệ thống.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SQL Query Example --}}
    <div class="card mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-code me-2"></i>SQL Query Example
            </h5>
        </div>
        <div class="card-body">
            <pre class="bg-dark text-light p-3 rounded mb-0" style="font-size: 0.85rem;"><code>SELECT 
    l.city,
    lg.legal_status,
    fr.furniture_state,
    COUNT(*) as property_count,
    AVG(f.price) as avg_price,
    MIN(f.price) as min_price,
    MAX(f.price) as max_price
FROM fact_property f
JOIN dim_location l ON f.location_id = l.location_id
JOIN dim_legal lg ON f.legal_id = lg.legal_id
JOIN dim_furniture fr ON f.furniture_id = fr.furniture_id
GROUP BY CUBE (l.city, lg.legal_status, fr.furniture_state)
HAVING COUNT(*) > 20
ORDER BY l.city, lg.legal_status, fr.furniture_state;</code></pre>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-database fs-2 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Quản lý Data Warehouse</h6>
                            <small class="text-muted">Xem thông tin kho dữ liệu</small>
                        </div>
                    </div>
                    <a href="{{ route('warehouse.index') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-cpu fs-2 text-success"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Dự đoán giá BDS</h6>
                            <small class="text-muted">Sử dụng mô hình phân loại</small>
                        </div>
                    </div>
                    <a href="{{ route('predict.index') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
