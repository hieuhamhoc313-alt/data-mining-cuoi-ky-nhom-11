@extends('layouts.app')

@section('title', 'Data Warehouse - Vietnam Real Estate Analytics')
@section('page-title', 'Data Warehouse')

@section('content')
<div class="container-fluid">
    {{-- Schema Info --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Star Schema Design:</strong> Fact table (fact_property) surrounded by dimension tables (dim_location, dim_legal, dim_furniture, dim_direction, dim_date)
            </div>
        </div>
    </div>

    {{-- Fact Table Stats --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="display-5 text-primary mb-2">
                        <i class="bi bi-hdd"></i>
                    </div>
                    <h4 class="mb-0">{{ number_format($factStats['total_records'] ?? 0) }}</h4>
                    <small class="text-muted">Total Records</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="display-5 text-success mb-2">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <h4 class="mb-0">{{ number_format($factStats['avg_price'] ?? 0, 1) }}</h4>
                    <small class="text-muted">Avg Price</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="display-5 text-warning mb-2">
                        <i class="bi bi-arrow-up"></i>
                    </div>
                    <h4 class="mb-0">{{ number_format($factStats['max_price'] ?? 0, 1) }}</h4>
                    <small class="text-muted">Max Price</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="display-5 text-danger mb-2">
                    <i class="bi bi-arrow-down"></i>
                </div>
                <div class="card-body">
                    <h4 class="mb-0">{{ number_format($factStats['min_price'] ?? 0, 1) }}</h4>
                    <small class="text-muted">Min Price</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="display-5 text-info mb-2">
                        <i class="bi bi-rulers"></i>
                    </div>
                    <h4 class="mb-0">{{ number_format($factStats['avg_area'] ?? 0, 0) }}</h4>
                    <small class="text-muted">Avg Area (m2)</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="display-5 text-secondary mb-2">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h4 class="mb-0">{{ number_format($factStats['total_value'] ?? 0, 0) }}</h4>
                    <small class="text-muted">Total Value (Ty)</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Dimension Tables --}}
    <div class="row g-4">
        {{-- dim_location --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-geo-alt me-2 text-primary"></i>dim_location
                    </h5>
                    <small class="text-muted">Thành phố và quận huyện</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Thành phố</th>
                                    <th class="text-end">Số lượng</th>
                                    <th class="text-end">Giá TB</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dimLocation as $loc)
                                <tr>
                                    <td>{{ $loc['city'] }}</td>
                                    <td class="text-end">{{ $loc['property_count'] }}</td>
                                    <td class="text-end">{{ $loc['avg_price'] }}</td>
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

        {{-- dim_legal --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-text me-2 text-success"></i>dim_legal
                    </h5>
                    <small class="text-muted">Tình trạng pháp lý</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Tình trạng</th>
                                    <th class="text-end">Số lượng</th>
                                    <th class="text-end">Giá TB</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dimLegal as $legal)
                                <tr>
                                    <td>
                                        <span class="badge bg-success">{{ $legal->legal_status ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-end">{{ number_format($legal->count) }}</td>
                                    <td class="text-end">{{ number_format($legal->avg_price, 2) }}</td>
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

        {{-- dim_furniture --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lamp me-2 text-warning"></i>dim_furniture
                    </h5>
                    <small class="text-muted">Tình trạng nội thất</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Tình trạng</th>
                                    <th class="text-end">Số lượng</th>
                                    <th class="text-end">Giá TB</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dimFurniture as $furniture)
                                <tr>
                                    <td>
                                        <span class="badge bg-warning">{{ $furniture->furniture_state ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-end">{{ number_format($furniture->count) }}</td>
                                    <td class="text-end">{{ number_format($furniture->avg_price, 2) }}</td>
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
    </div>

    {{-- Schema Diagram --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-diagram-3 me-2"></i>Star Schema Diagram
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <div class="p-4 bg-primary text-white rounded-3 d-inline-block mb-3">
                                    <strong>fact_property</strong>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col">
                                <div class="p-2 bg-light rounded d-inline-block">
                                    <small>dim_location</small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-2 bg-light rounded d-inline-block">
                                    <small>dim_legal</small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-2 bg-light rounded d-inline-block">
                                    <small>dim_furniture</small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-2 bg-light rounded d-inline-block">
                                    <small>dim_direction</small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-2 bg-light rounded d-inline-block">
                                    <small>dim_date</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Links --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Xem Iceberg Cube Analysis</h6>
                            <small class="text-muted">Phân tích đa chiều với GROUP BY CUBE</small>
                        </div>
                        <a href="{{ route('iceberg.index') }}" class="btn btn-primary">
                            Iceberg Cube <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
