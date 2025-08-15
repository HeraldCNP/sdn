@extends('sanctions.layout')

@section('title', 'Análisis de Red - Sistema de Sanciones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-diagram-3 me-2"></i>Análisis de Red de Relaciones</h2>
    <a href="{{ route('sanctions.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver al Dashboard
    </a>
</div>

<!-- Formulario de selección de análisis -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Tipo de Análisis</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('sanctions.network-analysis') }}">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="analysis_type" class="form-label">Seleccione el tipo de análisis:</label>
                            <select class="form-select" id="analysis_type" name="analysis_type">
                                <option value="most_connected" {{ $analysisType == 'most_connected' ? 'selected' : '' }}>
                                    Perfiles Más Conectados
                                </option>
                                <option value="relation_types" {{ $analysisType == 'relation_types' ? 'selected' : '' }}>
                                    Distribución de Tipos de Relaciones
                                </option>
                                <option value="active_vs_former" {{ $analysisType == 'active_vs_former' ? 'selected' : '' }}>
                                    Relaciones Activas vs Anteriores
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-graph-up me-2"></i>Ejecutar Análisis
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($results)
    @if($analysisType == 'most_connected')
        <!-- Perfiles más conectados -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-trophy me-2"></i>
                            Top 20 Perfiles Más Conectados
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Posición</th>
                                        <th>ProfileID</th>
                                        <th>Tipo de Entidad</th>
                                        <th>Rel. Salientes</th>
                                        <th>Rel. Entrantes</th>
                                        <th>Total Relaciones</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $index => $profile)
                                    <tr>
                                        <td>
                                            @if($index < 3)
                                                <span class="badge bg-warning">{{ $index + 1 }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ $profile->profile_id }}</strong></td>
                                        <td>
                                            @php
                                                $typeLabels = [
                                                    2 => 'Vessel',
                                                    3 => 'Entidad', 
                                                    4 => 'Individual'
                                                ];
                                                $typeClass = [
                                                    2 => 'bg-secondary',
                                                    3 => 'bg-warning',
                                                    4 => 'bg-info'
                                                ];
                                            @endphp
                                            <span class="badge {{ $typeClass[$profile->PartySubTypeID] ?? 'bg-dark' }}">
                                                {{ $typeLabels[$profile->PartySubTypeID] ?? 'Tipo ' . $profile->PartySubTypeID }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ number_format($profile->outgoing_relations) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ number_format($profile->incoming_relations) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success fs-6">{{ number_format($profile->total_relations) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('sanctions.search-profile', ['profile_id' => $profile->profile_id]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i>Ver Detalles
                                            </a>
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
        
    @elseif($analysisType == 'relation_types')
        <!-- Tipos de relaciones -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-pie-chart me-2"></i>
                            Distribución de Tipos de Relaciones
                        </h5>
                    </div>
                    <div class="card-body">
                        @php $total = $results->sum('count'); @endphp
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6><i class="bi bi-info-circle me-2"></i>Resumen</h6>
                                    <p class="mb-0">
                                        <strong>{{ number_format($total) }}</strong> relaciones totales distribuidas 
                                        en <strong>{{ $results->count() }}</strong> tipos diferentes.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tipo de Relación</th>
                                        <th>Cantidad</th>
                                        <th>Porcentaje</th>
                                        <th>Barra Visual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $relation)
                                    @php $percentage = ($relation->count / $total) * 100; @endphp
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">Tipo {{ $relation->RelationTypeID }}</span>
                                        </td>
                                        <td><strong>{{ number_format($relation->count) }}</strong></td>
                                        <td>{{ number_format($percentage, 2) }}%</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-primary" 
                                                     role="progressbar" 
                                                     style="width: {{ $percentage }}%" 
                                                     aria-valuenow="{{ $percentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ number_format($percentage, 1) }}%
                                                </div>
                                            </div>
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
        
    @elseif($analysisType == 'active_vs_former')
        <!-- Relaciones activas vs anteriores -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            Estado de las Relaciones
                        </h5>
                    </div>
                    <div class="card-body">
                        @php $total = $results->sum('count'); @endphp
                        
                        <div class="row mb-4">
                            @foreach($results as $status)
                            @php $percentage = ($status->count / $total) * 100; @endphp
                            <div class="col-md-6">
                                <div class="card text-center h-100 {{ $status->status == 'Activas' ? 'border-success' : 'border-warning' }}">
                                    <div class="card-body">
                                        <div class="display-4 {{ $status->status == 'Activas' ? 'text-success' : 'text-warning' }} mb-2">
                                            <i class="bi {{ $status->status == 'Activas' ? 'bi-check-circle' : 'bi-clock-history' }}"></i>
                                        </div>
                                        <h3 class="{{ $status->status == 'Activas' ? 'text-success' : 'text-warning' }}">
                                            {{ number_format($status->count) }}
                                        </h3>
                                        <p class="text-muted mb-1">{{ $status->status }}</p>
                                        <small class="text-muted">{{ number_format($percentage, 1) }}% del total</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Estado</th>
                                        <th>Cantidad</th>
                                        <th>Porcentaje</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $status)
                                    @php $percentage = ($status->count / $total) * 100; @endphp
                                    <tr>
                                        <td>
                                            <span class="badge {{ $status->status == 'Activas' ? 'bg-success' : 'bg-warning' }}">
                                                {{ $status->status }}
                                            </span>
                                        </td>
                                        <td><strong>{{ number_format($status->count) }}</strong></td>
                                        <td>{{ number_format($percentage, 2) }}%</td>
                                        <td>
                                            @if($status->status == 'Activas')
                                                Relaciones que se mantienen vigentes actualmente
                                            @else
                                                Relaciones que fueron válidas en el pasado pero ya no están activas
                                            @endif
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
    @endif
    
    <!-- Insights y recomendaciones -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Insights del Análisis</h5>
                </div>
                <div class="card-body">
                    @if($analysisType == 'most_connected')
                        <div class="row">
                            <div class="col-md-6">
                                <h6>🎯 Nodos Centrales</h6>
                                <p class="small text-muted">Los perfiles con más relaciones pueden ser entidades importantes en la red de sanciones. Use estos datos para:</p>
                                <ul class="small">
                                    <li>Identificar actores clave en redes criminales</li>
                                    <li>Priorizar investigaciones de compliance</li>
                                    <li>Detectar estructuras organizacionales complejas</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>⚠️ Consideraciones</h6>
                                <ul class="small">
                                    <li>Un alto número de relaciones puede indicar importancia estratégica</li>
                                    <li>Verifique si las relaciones son actuales o históricas</li>
                                    <li>Analice el contexto de cada relación individualmente</li>
                                </ul>
                            </div>
                        </div>
                    @elseif($analysisType == 'relation_types')
                        <div class="alert alert-info">
                            <h6>📊 Distribución de Relaciones</h6>
                            <p class="mb-0">La distribución de tipos de relaciones ayuda a entender los patrones más comunes en la red de sanciones. 
                            Los tipos más frecuentes pueden indicar las formas más comunes de asociación entre entidades sancionadas.</p>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <h6>⏰ Estado Temporal de Relaciones</h6>
                            <p class="mb-0">
                                @if($results->where('status', 'Anteriores')->first())
                                <strong>{{ number_format($results->where('status', 'Anteriores')->first()->count) }}</strong> relaciones están marcadas como "anteriores", 
                                lo que indica que fueron válidas en el pasado pero ya no están activas. Esto es útil para análisis históricos y tracking de cambios en redes.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
@else
    <!-- Estado inicial -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <div class="display-1 text-muted mb-3">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                    <h4>Análisis de Red de Relaciones</h4>
                    <p class="text-muted mb-4">
                        Seleccione un tipo de análisis para comenzar a explorar las conexiones y patrones 
                        en la red de relaciones entre perfiles sancionados.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h6 class="text-primary">Perfiles Conectados</h6>
                                    <p class="small text-muted">Identifica los perfiles con mayor número de conexiones en la red</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-body">
                                    <h6 class="text-info">Tipos de Relaciones</h6>
                                    <p class="small text-muted">Analiza la distribución de diferentes tipos de relaciones</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="text-success">Estado Temporal</h6>
                                    <p class="small text-muted">Compara relaciones activas versus anteriores</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
