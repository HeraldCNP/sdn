@extends('sanctions.layout')

@section('title', 'Dashboard - Sistema de Sanciones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2 me-2"></i>Dashboard General</h2>
    <span class="badge bg-success fs-6">Sistema Activo</span>
</div>

<!-- Estadísticas Principales -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card text-center h-100">
            <div class="card-body">
                <div class="display-4 text-primary mb-2">
                    <i class="bi bi-people"></i>
                </div>
                <h3 class="text-primary">{{ number_format($stats['total_profiles']) }}</h3>
                <p class="text-muted mb-0">Total Perfiles</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card text-center h-100">
            <div class="card-body">
                <div class="display-4 text-info mb-2">
                    <i class="bi bi-person"></i>
                </div>
                <h3 class="text-info">{{ number_format($stats['individuals']) }}</h3>
                <p class="text-muted mb-0">Individuos</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card text-center h-100">
            <div class="card-body">
                <div class="display-4 text-warning mb-2">
                    <i class="bi bi-building"></i>
                </div>
                <h3 class="text-warning">{{ number_format($stats['entities']) }}</h3>
                <p class="text-muted mb-0">Entidades</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card text-center h-100">
            <div class="card-body">
                <div class="display-4 text-secondary mb-2">
                    <i class="bi bi-ship"></i>
                </div>
                <h3 class="text-secondary">{{ number_format($stats['vessels']) }}</h3>
                <p class="text-muted mb-0">Vessels</p>
            </div>
        </div>
    </div>
</div>

<!-- Segunda fila de estadísticas -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card stats-card text-center h-100">
            <div class="card-body">
                <div class="display-4 text-success mb-2">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <h3 class="text-success">{{ number_format($stats['total_relations']) }}</h3>
                <p class="text-muted mb-0">Total Relaciones</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card stats-card text-center h-100">
            <div class="card-body">
                <div class="display-4 text-danger mb-2">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <h3 class="text-danger">{{ number_format($stats['total_countries']) }}</h3>
                <p class="text-muted mb-0">Países</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card stats-card text-center h-100">
            <div class="card-body">
                <div class="display-4 text-dark mb-2">
                    <i class="bi bi-card-text"></i>
                </div>
                <h3 class="text-dark">{{ number_format($stats['total_aliases']) }}</h3>
                <p class="text-muted mb-0">Total Aliases</p>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos y análisis -->
<div class="row">
    <!-- Top Tipos de Relaciones -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Tipos de Relaciones Más Comunes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tipo de Relación</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = $topRelationTypes->sum('count'); @endphp
                            @foreach($topRelationTypes as $index => $type)
                            <tr>
                                <td>
                                    <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                    Tipo {{ $type->RelationTypeID }}
                                </td>
                                <td class="text-end">{{ number_format($type->count) }}</td>
                                <td class="text-end">
                                    <span class="text-muted">{{ number_format(($type->count / $total) * 100, 1) }}%</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Componentes de Nombres -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Componentes de Nombres Más Comunes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tipo de Componente</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalNames = $topNameComponents->sum('count'); @endphp
                            @foreach($topNameComponents as $index => $component)
                            <tr>
                                <td>
                                    <span class="badge bg-info me-2">{{ $index + 1 }}</span>
                                    {{ $component->Description }}
                                </td>
                                <td class="text-end">{{ number_format($component->count) }}</td>
                                <td class="text-end">
                                    <span class="text-muted">{{ number_format(($component->count / $totalNames) * 100, 1) }}%</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enlaces rápidos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Acciones Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('sanctions.search-profile') }}" class="btn btn-outline-primary btn-block w-100">
                            <i class="bi bi-search me-2"></i>Buscar Perfil
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('sanctions.network-analysis') }}" class="btn btn-outline-info btn-block w-100">
                            <i class="bi bi-diagram-3 me-2"></i>Análisis de Red
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('sanctions.entity-analysis') }}" class="btn btn-outline-warning btn-block w-100">
                            <i class="bi bi-people me-2"></i>Analizar Entidades
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('sanctions.name-analysis') }}" class="btn btn-outline-success btn-block w-100">
                            <i class="bi bi-card-text me-2"></i>Análisis de Nombres
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Información del sistema -->
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info">
            <h6><i class="bi bi-info-circle me-2"></i>Información del Sistema</h6>
            <p class="mb-0">
                Este sistema contiene <strong>{{ number_format($stats['total_profiles']) }} perfiles</strong> 
                con <strong>{{ number_format($stats['total_relations']) }} relaciones</strong> 
                distribuidas en <strong>{{ number_format($stats['total_countries']) }} países</strong>. 
                Los datos están actualizados y listos para consulta y análisis de compliance.
            </p>
        </div>
    </div>
</div>

@endsection
