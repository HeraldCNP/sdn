@extends('sanctions.layout')

@section('title', 'Búsqueda de Perfiles - Sistema de Sanciones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-search me-2"></i>Búsqueda por Perfil</h2>
    <a href="{{ route('sanctions.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver al Dashboard
    </a>
</div>

<!-- Formulario de búsqueda -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Parámetros de Búsqueda</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('sanctions.search-profile') }}">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="profile_id" class="form-label">ProfileID a buscar:</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="profile_id" 
                                   name="profile_id" 
                                   value="{{ $profileId }}" 
                                   placeholder="Ej: 36, 173, 306..."
                                   min="1">
                            <div class="form-text">Ingrese el ID del perfil que desea consultar</div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="d-grid gap-2 d-md-block w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-2"></i>Buscar Perfil
                                </button>
                                <a href="{{ route('sanctions.search-profile') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Limpiar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($results)
    @if($results['profile'])
        <!-- Información básica del perfil -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success">
                    <h5><i class="bi bi-check-circle me-2"></i>Perfil Encontrado</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>ProfileID:</strong> {{ $results['profile']->ID }}
                        </div>
                        <div class="col-md-3">
                            <strong>Tipo de Entidad:</strong> 
                            @php
                                $typeLabels = [
                                    2 => 'Vessel',
                                    3 => 'Entidad',
                                    4 => 'Individual'
                                ];
                            @endphp
                            <span class="badge bg-info">
                                {{ $typeLabels[$results['profile']->PartySubTypeID] ?? 'Tipo ' . $results['profile']->PartySubTypeID }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong>FixedRef:</strong> {{ $results['profile']->FixedRef }}
                        </div>
                        <div class="col-md-3">
                            <strong>Identidades:</strong> {{ $results['identities']->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Información de Identidades -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Identidades ({{ $results['identities']->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($results['identities']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>FixedRef</th>
                                            <th>Primaria</th>
                                            <th>Falsa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results['identities'] as $identity)
                                        <tr>
                                            <td>{{ $identity->ID }}</td>
                                            <td>{{ $identity->FixedRef }}</td>
                                            <td>
                                                <span class="badge {{ $identity->Primary_ ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $identity->Primary_ ? 'Sí' : 'No' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $identity->False_ ? 'bg-warning' : 'bg-success' }}">
                                                    {{ $identity->False_ ? 'Sí' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">No se encontraron identidades para este perfil.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Componentes de Nombres -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-card-text me-2"></i>Componentes de Nombres ({{ $results['name_components']->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($results['name_components']->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($results['name_components'] as $component)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $component->type }}</h6>
                                        <small class="text-muted">ID: {{ $component->type_id }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No se encontraron componentes de nombres para este perfil.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Relaciones -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-diagram-3 me-2"></i>
                            Relaciones ({{ $results['relations']->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($results['relations']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Dirección</th>
                                            <th>Perfil Conectado</th>
                                            <th>Tipo de Relación</th>
                                            <th>Estado</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results['relations'] as $relation)
                                        <tr>
                                            <td>
                                                <span class="badge {{ $relation->direction == 'Saliente' ? 'bg-primary' : 'bg-info' }}">
                                                    <i class="bi {{ $relation->direction == 'Saliente' ? 'bi-arrow-right' : 'bi-arrow-left' }} me-1"></i>
                                                    {{ $relation->direction }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $relation->connected_profile }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $relation->RelationTypeID }}</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $relation->Former ? 'bg-warning' : 'bg-success' }}">
                                                    {{ $relation->Former ? 'Anterior' : 'Activa' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('sanctions.search-profile', ['profile_id' => $relation->connected_profile]) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye me-1"></i>Ver
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Este perfil no tiene relaciones registradas con otros perfiles.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Perfil no encontrado -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger">
                    <h5><i class="bi bi-exclamation-triangle me-2"></i>Perfil No Encontrado</h5>
                    <p class="mb-0">No se encontró ningún perfil con el ID <strong>{{ $profileId }}</strong>. 
                    Verifique el número ingresado e intente nuevamente.</p>
                </div>
            </div>
        </div>
    @endif
@elseif($profileId)
    <!-- Error en búsqueda -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning">
                <h5><i class="bi bi-exclamation-triangle me-2"></i>Sin Resultados</h5>
                <p class="mb-0">No se pudo encontrar información para el ProfileID <strong>{{ $profileId }}</strong>.</p>
            </div>
        </div>
    </div>
@else
    <!-- Instrucciones iniciales -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <div class="display-1 text-muted mb-3">
                        <i class="bi bi-search"></i>
                    </div>
                    <h4>Consulta de Perfiles</h4>
                    <p class="text-muted mb-4">
                        Ingrese un ProfileID para obtener información detallada incluyendo identidades, 
                        componentes de nombres y relaciones con otros perfiles.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h6 class="text-primary">Ejemplos de ProfileID</h6>
                                    <p class="small text-muted mb-2">Pruebe con estos IDs:</p>
                                    <div class="d-flex gap-2 flex-wrap justify-content-center">
                                        <a href="?profile_id=36" class="badge bg-primary text-decoration-none">36</a>
                                        <a href="?profile_id=173" class="badge bg-primary text-decoration-none">173</a>
                                        <a href="?profile_id=306" class="badge bg-primary text-decoration-none">306</a>
                                        <a href="?profile_id=424" class="badge bg-primary text-decoration-none">424</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-body">
                                    <h6 class="text-info">Información Mostrada</h6>
                                    <ul class="small text-muted list-unstyled">
                                        <li><i class="bi bi-check me-1"></i>Datos básicos del perfil</li>
                                        <li><i class="bi bi-check me-1"></i>Identidades asociadas</li>
                                        <li><i class="bi bi-check me-1"></i>Componentes de nombres</li>
                                        <li><i class="bi bi-check me-1"></i>Relaciones con otros perfiles</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="text-success">Casos de Uso</h6>
                                    <ul class="small text-muted list-unstyled">
                                        <li><i class="bi bi-check me-1"></i>Screening de entidades</li>
                                        <li><i class="bi bi-check me-1"></i>Análisis de compliance</li>
                                        <li><i class="bi bi-check me-1"></i>Investigación de relaciones</li>
                                        <li><i class="bi bi-check me-1"></i>Auditoría de datos</li>
                                    </ul>
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
