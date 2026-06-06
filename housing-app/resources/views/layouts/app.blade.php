<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vietnam Real Estate Analytics')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #1abc9c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, var(--dark-bg) 0%, #1a252f 100%);
            padding-top: 20px;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .sidebar .nav-link i {
            font-size: 1.2rem;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }

        .stat-card {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #2980b9 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            min-height: 140px;
        }

        .stat-card.success {
            background: linear-gradient(135deg, var(--success-color) 0%, #1e8449 100%);
        }

        .stat-card.warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #d68910 100%);
        }

        .stat-card.danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #c0392b 100%);
        }

        .stat-card .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-card .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .navbar-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background-color: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.08);
        }

        .badge-segment {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-low {
            background-color: var(--success-color);
        }

        .badge-medium {
            background-color: var(--warning-color);
        }

        .badge-high {
            background-color: var(--danger-color);
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                width: 0;
                padding: 0;
                overflow: hidden;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar.show {
                width: 250px;
            }
        }

        @media (max-width: 767.98px) {
            .stat-card {
                padding: 15px;
            }

            .stat-card .stat-value {
                font-size: 1.5rem;
            }

            .page-title {
                font-size: 1.4rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="sidebar" id="sidebar">
        <div class="text-center mb-4">
            <h4 class="text-white mb-0">
                <i class="bi bi-building"></i> Housing VN
            </h4>
            <small class="text-white-50">Data Mining System</small>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('analytics.index') }}" class="nav-link {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i> Analytics
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('warehouse.index') }}" class="nav-link {{ request()->routeIs('warehouse.*') ? 'active' : '' }}">
                    <i class="bi bi-database"></i> Data Warehouse
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('iceberg.index') }}" class="nav-link {{ request()->routeIs('iceberg.*') ? 'active' : '' }}">
                    <i class="bi bi-cube"></i> Iceberg Cube
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('predict.index') }}" class="nav-link {{ request()->routeIs('predict.*') ? 'active' : '' }}">
                    <i class="bi bi-cpu"></i> Prediction
                </a>
            </li>
        </ul>
    </nav>

    <div class="main-content">
        <nav class="navbar-custom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary d-lg-none" id="toggleSidebar">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div>
                <span class="text-muted">
                    <i class="bi bi-calendar3"></i> {{ now()->format('d/m/Y') }}
                </span>
            </div>
        </nav>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('toggleSidebar')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>
</html>
