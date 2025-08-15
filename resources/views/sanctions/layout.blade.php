<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Consulta de Sanciones')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            background-color: #2c3e50;
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.375rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #34495e;
            color: #ffffff;
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
        }
        .stats-card {
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <h5 class="text-white mb-4">
                    <i class="bi bi-shield-check me-2"></i>
                    Sistema de Sanciones
                </h5>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sanctions.dashboard') ? 'active' : '' }}" 
                           href="{{ route('sanctions.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sanctions.search-profile') ? 'active' : '' }}" 
                           href="{{ route('sanctions.search-profile') }}">
                            <i class="bi bi-search me-2"></i>
                            Búsqueda por Perfil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sanctions.network-analysis') ? 'active' : '' }}" 
                           href="{{ route('sanctions.network-analysis') }}">
                            <i class="bi bi-diagram-3 me-2"></i>
                            Análisis de Red
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sanctions.entity-analysis') ? 'active' : '' }}" 
                           href="{{ route('sanctions.entity-analysis') }}">
                            <i class="bi bi-people me-2"></i>
                            Análisis de Entidades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sanctions.name-analysis') ? 'active' : '' }}" 
                           href="{{ route('sanctions.name-analysis') }}">
                            <i class="bi bi-card-text me-2"></i>
                            Análisis de Nombres
                        </a>
                    </li>
                </ul>

                <hr class="my-4 text-white">
                
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Sistema de consulta y análisis de listas de sanciones internacionales
                </small>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
