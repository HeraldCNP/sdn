@extends('sanctions.layout')

@section('title', 'Análisis de Nombres - Sistema de Sanciones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-card-text me-2"></i>Análisis de Componentes de Nombres</h2>
    <a href="{{ route('sanctions.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver al Dashboard
    </a>
</div>

<!-- Formulario de análisis -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Tipo de Análisis</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('sanctions.name-analysis') }}">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="component_type" class="form-label">Tipo de análisis:</label>
                            <select class="form-select" id="component_type" name="component_type">
                                <option value="statistics" {{ $componentType == 'statistics' ? 'selected' : '' }}>
                                    Estadísticas de Componentes
                                </option>
                                <option value="profiles_by_component" {{ $componentType == 'profiles_by_component' ? 'selected' : '' }}>
                                    Perfiles por Componente Específico
                                </option>
                            </select>
                        </div>
                        @if($componentType == 'profiles_by_component')
                        <div class="col-md-4">
                            <label for="type_id" class="form-label">Componente específico:</label>
                            <select class="form-select" id="type_id" name="type_id">
                                <option value="">Seleccionar componente...</option>
                                @foreach($componentTypes as $type)
                                <option value="{{ $type->ID }}" {{ request('type_id') == $type->ID ? 'selected' : '' }}>
                                    {{ $type->Description }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-2"></i>Analizar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($results)
    @if($componentType == 'statistics')
        <!-- Estadísticas de componentes -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-bar-chart me-2"></i>
                            Distribución de Componentes de Nombres
                        </h5>
                    </div>
                    <div class="card-body">
                        @php $total = $results->sum('count'); @endphp
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6><i class="bi bi-info-circle me-2"></i>Resumen</h6>
                                    <p class="mb-0">
                                        <strong>{{ number_format($total) }}</strong> componentes totales distribuidos 
                                        en <strong>{{ $results->count() }}</strong> tipos diferentes.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Ranking</th>
                                        <th>Tipo de Componente</th>
                                        <th>Cantidad</th>
                                        <th>Porcentaje</th>
                                        <th>Barra Visual</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $index => $component)
                                    @php $percentage = ($component->count / $total) * 100; @endphp
                                    <tr>
                                        <td>
                                            @if($index < 3)
                                                <span class="badge bg-warning">{{ $index + 1 }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $component->Description }}</strong>
                                            <small class="text-muted d-block">ID: {{ $component->ID }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary fs-6">{{ number_format($component->count) }}</span>
                                        </td>
                                        <td>{{ number_format($percentage, 2) }}%</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" 
                                                     role="progressbar" 
                                                     style="width: {{ min($percentage, 100) }}%; background-color: {{ $this->getColorForIndex($index) }}" 
                                                     aria-valuenow="{{ $percentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ number_format($percentage, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('sanctions.name-analysis', ['component_type' => 'profiles_by_component', 'type_id' => $component->ID]) }}" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye me-1"></i>Ver Perfiles
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
        
        <!-- Insights -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Análisis de Componentes</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>🔤 Componentes Más Comunes</h6>
                                <ul class="small">
                                    @foreach($results->take(3) as $component)
                                    <li><strong>{{ $component->Description }}</strong>: {{ number_format($component->count) }} ocurrencias</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>📊 Aplicaciones</h6>
                                <ul class="small">
                                    <li>Matching y detección de aliases</li>
                                    <li>Análisis de completitud de datos</li>
                                    <li>Identificación de patrones de nomenclatura</li>
                                    <li>Optimización de algoritmos de búsqueda</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    @elseif($componentType == 'profiles_by_component' && $results)
        <!-- Perfiles por componente específico -->
        @php 
            $selectedType = $componentTypes->where('ID', request('type_id'))->first();
        @endphp
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-list me-2"></i>
                            Perfiles con Componente: {{ $selectedType ? $selectedType->Description : 'N/A' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($selectedType)
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            Mostrando perfiles que contienen el componente de nombre 
                            <strong>{{ $selectedType->Description }}</strong> (Primeros 100 resultados).
                        </div>
                        @endif
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ProfileID</th>
                                        <th>Tipo de Entidad</th>
                                        <th>Componente</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $profile)
                                    <tr>
                                        <td><strong>{{ $profile->ProfileID }}</strong></td>
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
                                            <span class="badge bg-light text-dark">{{ $profile->ComponentType }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('sanctions.search-profile', ['profile_id' => $profile->ProfileID]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i>Ver Detalles
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($results->count() >= 100)
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Se muestran los primeros 100 resultados. Hay más perfiles disponibles para este componente.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
    
@else
    <!-- Estado inicial -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <div class="display-1 text-muted mb-3">
                        <i class="bi bi-card-text"></i>
                    </div>
                    <h4>Análisis de Componentes de Nombres</h4>
                    <p class="text-muted mb-4">
                        Explore la distribución y uso de los diferentes tipos de componentes 
                        que conforman los nombres en el sistema de sanciones.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h6 class="text-primary">📊 Estadísticas Generales</h6>
                                    <p class="small text-muted">Vea la distribución de todos los tipos de componentes de nombres disponibles</p>
                                    <div class="mt-3">
                                        <strong class="text-primary">Componentes Disponibles:</strong>
                                        <div class="mt-2">
                                            @foreach($componentTypes->take(5) as $type)
                                            <span class="badge bg-light text-dark me-1 mb-1">{{ $type->Description }}</span>
                                            @endforeach
                                            @if($componentTypes->count() > 5)
                                            <span class="text-muted">+{{ $componentTypes->count() - 5 }} más</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-body">
                                    <h6 class="text-info">🔍 Análisis Específico</h6>
                                    <p class="small text-muted">Examine qué perfiles contienen componentes de nombres específicos</p>
                                    <div class="mt-3">
                                        <strong class="text-info">Casos de Uso:</strong>
                                        <ul class="small text-muted list-unstyled mt-2">
                                            <li><i class="bi bi-check me-1"></i>Análisis de calidad de datos</li>
                                            <li><i class="bi bi-check me-1"></i>Detección de patrones</li>
                                            <li><i class="bi bi-check me-1"></i>Optimización de búsquedas</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Información sobre tipos de componentes -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Tipos de Componentes de Nombres</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="text-primary">👤 Componentes Personales</h6>
                        <ul class="small">
                            <li><strong>First Name</strong>: Nombres de pila</li>
                            <li><strong>Last Name</strong>: Apellidos</li>
                            <li><strong>Middle Name</strong>: Nombres intermedios</li>
                            <li><strong>Nickname</strong>: Apodos o alias</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-warning">🏢 Componentes de Entidades</h6>
                        <ul class="small">
                            <li><strong>Entity Name</strong>: Nombres de organizaciones</li>
                            <li><strong>Vessel Name</strong>: Nombres de embarcaciones</li>
                            <li><strong>Aircraft Name</strong>: Nombres de aeronaves</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-info">🌍 Componentes Culturales</h6>
                        <ul class="small">
                            <li><strong>Patronymic</strong>: Nombres patronímicos</li>
                            <li><strong>Matronymic</strong>: Nombres matronímicos</li>
                            <li><strong>Maiden Name</strong>: Nombres de soltera</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Cambiar formulario dinámicamente
document.getElementById('component_type').addEventListener('change', function() {
    if (this.value === 'profiles_by_component') {
        // Recargar para mostrar el select de componentes
        this.form.submit();
    }
});
</script>
@endpush
