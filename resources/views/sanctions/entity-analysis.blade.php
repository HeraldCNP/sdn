@extends('sanctions.layout')

@section('title', 'Análisis de Entidades - Sistema de Sanciones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people me-2"></i>Análisis de Entidades</h2>
    <a href="{{ route('sanctions.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver al Dashboard
    </a>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filtros de Análisis</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('sanctions.entity-analysis') }}">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="entity_type" class="form-label">Tipo de Entidad:</label>
                            <select class="form-select" id="entity_type" name="entity_type">
                                <option value="all" {{ $entityType == 'all' ? 'selected' : '' }}>Todos los tipos</option>
                                <option value="4" {{ $entityType == '4' ? 'selected' : '' }}>Individuos (Tipo 4)</option>
                                <option value="3" {{ $entityType == '3' ? 'selected' : '' }}>Entidades (Tipo 3)</option>
                                <option value="2" {{ $entityType == '2' ? 'selected' : '' }}>Vessels (Tipo 2)</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-2"></i>Filtrar Resultados
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas por tipo -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Distribución por Tipo de Entidad</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($statistics as $stat)
                    @php 
                        $percentage = ($stat->count / $statistics->sum('count')) * 100;
                        $colorClass = match($stat->PartySubTypeID) {
                            2 => 'secondary',
                            3 => 'warning',
                            4 => 'info',
                            default => 'dark'
                        };
                    @endphp
                    <div class="col-md-4 mb-3">
                        <div class="card border-{{ $colorClass }} h-100">
                            <div class="card-body text-center">
                                <div class="display-4 text-{{ $colorClass }} mb-2">
                                    <i class="bi {{ $stat->PartySubTypeID == 4 ? 'bi-person' : ($stat->PartySubTypeID == 3 ? 'bi-building' : 'bi-ship') }}"></i>
                                </div>
                                <h3 class="text-{{ $colorClass }}">{{ number_format($stat->count) }}</h3>
                                <p class="text-muted mb-1">{{ $stat->type_name }}</p>
                                <small class="text-muted">{{ number_format($percentage, 1) }}% del total</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resultados filtrados -->
@if($results && $results->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-list me-2"></i>
                    Perfiles 
                    @if($entityType != 'all')
                        - {{ $statistics->where('PartySubTypeID', $entityType)->first()->type_name ?? 'Tipo ' . $entityType }}
                    @endif
                    (Mostrando primeros 50)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ProfileID</th>
                                <th>Tipo</th>
                                <th>Componentes de Nombres</th>
                                <th>Total Componentes</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $profile)
                            <tr>
                                <td><strong>{{ $profile->ID }}</strong></td>
                                <td>
                                    @php
                                        $typeLabels = [
                                            2 => 'Vessel',
                                            3 => 'Entidad',
                                            4 => 'Individual'
                                        ];
                                        $typeClasses = [
                                            2 => 'bg-secondary',
                                            3 => 'bg-warning',
                                            4 => 'bg-info'
                                        ];
                                    @endphp
                                    <span class="badge {{ $typeClasses[$profile->PartySubTypeID] ?? 'bg-dark' }}">
                                        {{ $typeLabels[$profile->PartySubTypeID] ?? 'Tipo ' . $profile->PartySubTypeID }}
                                    </span>
                                </td>
                                <td>
                                    @if($profile->name_components)
                                        @php
                                            $components = explode(',', $profile->name_components);
                                            $maxShow = 3;
                                        @endphp
                                        @foreach(array_slice($components, 0, $maxShow) as $component)
                                            <span class="badge bg-light text-dark me-1">{{ trim($component) }}</span>
                                        @endforeach
                                        @if(count($components) > $maxShow)
                                            <span class="text-muted">+{{ count($components) - $maxShow }} más</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Sin componentes</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $profile->component_count }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('sanctions.search-profile', ['profile_id' => $profile->ID]) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>Ver Detalles
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($results->count() >= 50)
                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Se muestran los primeros 50 resultados. Use los filtros para refinar la búsqueda.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@else
<!-- Estado inicial o sin resultados -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center">
                <div class="display-1 text-muted mb-3">
                    <i class="bi bi-people"></i>
                </div>
                <h4>Análisis de Entidades por Tipo</h4>
                <p class="text-muted mb-4">
                    Explore los diferentes tipos de entidades en el sistema de sanciones. 
                    Use los filtros para analizar individuos, entidades corporativas o vessels.
                </p>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card border-info">
                            <div class="card-body">
                                <h6 class="text-info">👤 Individuos</h6>
                                <p class="small text-muted">{{ number_format($statistics->where('PartySubTypeID', 4)->first()->count ?? 0) }} personas físicas sancionadas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning">
                            <div class="card-body">
                                <h6 class="text-warning">🏢 Entidades</h6>
                                <p class="small text-muted">{{ number_format($statistics->where('PartySubTypeID', 3)->first()->count ?? 0) }} organizaciones y empresas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-secondary">
                            <div class="card-body">
                                <h6 class="text-secondary">🚢 Vessels</h6>
                                <p class="small text-muted">{{ number_format($statistics->where('PartySubTypeID', 2)->first()->count ?? 0) }} embarcaciones y vehículos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Información adicional -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información sobre Tipos de Entidades</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="text-info">Individuos (Tipo 4)</h6>
                        <ul class="small">
                            <li>Personas físicas bajo sanciones</li>
                            <li>Incluyen nombres, apellidos, apodos</li>
                            <li>Pueden tener múltiples identidades</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-warning">Entidades (Tipo 3)</h6>
                        <ul class="small">
                            <li>Empresas, organizaciones, gobiernos</li>
                            <li>Nombres comerciales y legales</li>
                            <li>Pueden incluir subsidiarias</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-secondary">Vessels (Tipo 2)</h6>
                        <ul class="small">
                            <li>Embarcaciones y aeronaves</li>
                            <li>Incluyen nombres y registros</li>
                            <li>Pueden cambiar de nombre/bandera</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
