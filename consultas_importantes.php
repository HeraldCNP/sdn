<?php

// Archivo de consultas importantes para análisis de sanciones
// Ejecutar con: php -f consultas_importantes.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CONSULTAS IMPORTANTES PARA ANÁLISIS DE SANCIONES ===\n\n";

// 1. BÚSQUEDA POR NOMBRE
echo "1. Búsqueda por nombre (ejemplo: nombres que contengan 'PUTIN'):\n";
$nombres = DB::table('documented_names as dn')
    ->join('identities as i', 'dn.IdentityID', '=', 'i.id')
    ->join('profiles as p', 'i.ProfileID', '=', 'p.id')
    ->where(function($query) {
        $query->where('dn.FirstName', 'LIKE', '%PUTIN%')
              ->orWhere('dn.LastName', 'LIKE', '%PUTIN%');
    })
    ->select('p.id as ProfileID', 'dn.FirstName', 'dn.LastName', 'dn.FullName', 'p.PartySubTypeID')
    ->limit(5)
    ->get();

foreach ($nombres as $nombre) {
    echo "- ProfileID: {$nombre->ProfileID}, Nombre: {$nombre->FirstName} {$nombre->LastName}\n";
}

// 2. ANÁLISIS DE RELACIONES
echo "\n2. Análisis de relaciones entre perfiles:\n";
$relaciones = DB::table('profile_relationships as pr')
    ->join('profiles as p1', 'pr.FromProfileID', '=', 'p1.id')
    ->join('profiles as p2', 'pr.ToProfileID', '=', 'p2.id')
    ->join('documented_names as dn1', function($join) {
        $join->on('p1.id', '=', 'dn1.IdentityID')
             ->where('dn1.Primary', '=', 'true');
    })
    ->join('documented_names as dn2', function($join) {
        $join->on('p2.id', '=', 'dn2.IdentityID')
             ->where('dn2.Primary', '=', 'true');
    })
    ->select(
        'pr.FromProfileID',
        'pr.ToProfileID', 
        'pr.RelationTypeID',
        'dn1.FullName as FromName',
        'dn2.FullName as ToName',
        'pr.Former'
    )
    ->limit(10)
    ->get();

foreach ($relaciones as $rel) {
    echo "- {$rel->FromName} ({$rel->FromProfileID}) -> {$rel->ToName} ({$rel->ToProfileID}) [Tipo: {$rel->RelationTypeID}]\n";
}

// 3. ESTADÍSTICAS GENERALES
echo "\n3. Estadísticas generales:\n";
$stats = [
    'Total perfiles' => DB::table('profiles')->count(),
    'Total individuos' => DB::table('profiles')->where('PartySubTypeID', 4)->count(),
    'Total entidades' => DB::table('profiles')->where('PartySubTypeID', '!=', 4)->count(),
    'Total relaciones' => DB::table('profile_relationships')->count(),
    'Relaciones activas' => DB::table('profile_relationships')->where('Former', 'false')->count(),
    'Relaciones anteriores' => DB::table('profile_relationships')->where('Former', 'true')->count(),
];

foreach ($stats as $key => $value) {
    echo "- {$key}: {$value}\n";
}

// 4. TIPOS DE PARTES MÁS COMUNES
echo "\n4. Tipos de partes (SubTypes) más comunes:\n";
$subtipos = DB::table('profiles')
    ->select('PartySubTypeID', DB::raw('COUNT(*) as cantidad'))
    ->groupBy('PartySubTypeID')
    ->orderBy('cantidad', 'desc')
    ->get();

foreach ($subtipos as $subtipo) {
    echo "- Subtipo {$subtipo->PartySubTypeID}: {$subtipo->cantidad} registros\n";
}

// 5. PERFILES CON MÁS ALIASES
echo "\n5. Perfiles con más aliases (nombres alternativos):\n";
$aliases = DB::table('documented_names as dn')
    ->join('identities as i', 'dn.IdentityID', '=', 'i.id')
    ->join('profiles as p', 'i.ProfileID', '=', 'p.id')
    ->select('p.id', DB::raw('COUNT(dn.id) as total_nombres'))
    ->groupBy('p.id')
    ->orderBy('total_nombres', 'desc')
    ->limit(10)
    ->get();

foreach ($aliases as $alias) {
    // Obtener el nombre principal
    $nombrePrincipal = DB::table('documented_names as dn')
        ->join('identities as i', 'dn.IdentityID', '=', 'i.id')
        ->where('i.ProfileID', $alias->id)
        ->where('dn.Primary', 'true')
        ->value('dn.FullName');
    
    echo "- ProfileID {$alias->id}: {$nombrePrincipal} ({$alias->total_nombres} nombres)\n";
}

// 6. ANÁLISIS DE COMPONENTES DE NOMBRES
echo "\n6. Tipos de componentes de nombres más comunes:\n";
$componentes = DB::table('name_part_groups as npg')
    ->join('name_part_types as npt', 'npg.NamePartTypeID', '=', 'npt.ID')
    ->select('npt.NamePartType', DB::raw('COUNT(npg.id) as cantidad'))
    ->groupBy('npt.NamePartType')
    ->orderBy('cantidad', 'desc')
    ->get();

foreach ($componentes as $comp) {
    echo "- {$comp->NamePartType}: {$comp->cantidad} ocurrencias\n";
}

echo "\n=== FIN DE CONSULTAS ===\n";
