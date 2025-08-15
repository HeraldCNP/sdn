<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CONSULTAS IMPORTANTES PARA ANÁLISIS DE SANCIONES ===\n\n";

// 1. ESTADÍSTICAS GENERALES
echo "1. Estadísticas generales del dataset:\n";
$stats = [
    'Total perfiles' => DB::table('profiles')->count(),
    'Total individuos (Tipo 4)' => DB::table('profiles')->where('PartySubTypeID', 4)->count(),
    'Total entidades (Tipo 3)' => DB::table('profiles')->where('PartySubTypeID', 3)->count(),
    'Total vessels (Tipo 2)' => DB::table('profiles')->where('PartySubTypeID', 2)->count(),
    'Total identidades' => DB::table('identities')->count(),
    'Total aliases' => DB::table('aliases')->count(),
    'Total relaciones' => DB::table('profile_relationships')->count(),
    'Relaciones activas' => DB::table('profile_relationships')->where('Former', 0)->count(),
    'Relaciones anteriores' => DB::table('profile_relationships')->where('Former', 1)->count(),
];

foreach ($stats as $key => $value) {
    echo "- {$key}: {$value}\n";
}

// 2. ANÁLISIS DE TIPOS DE PARTES DE NOMBRES
echo "\n2. Análisis de componentes de nombres disponibles:\n";
$namePartTypes = DB::table('name_part_types')->orderBy('ID')->get();
foreach ($namePartTypes as $type) {
    $count = DB::table('name_part_groups')->where('NamePartTypeID', $type->ID)->count();
    echo "- {$type->Description} (ID: {$type->ID}): {$count} ocurrencias\n";
}

// 3. PERFILES CON MÁS IDENTIDADES
echo "\n3. Perfiles con múltiples identidades:\n";
$multipleIdentities = DB::table('identities')
    ->select('ProfileID', DB::raw('COUNT(*) as identity_count'))
    ->groupBy('ProfileID')
    ->having('identity_count', '>', 1)
    ->orderBy('identity_count', 'desc')
    ->limit(10)
    ->get();

foreach ($multipleIdentities as $profile) {
    echo "- ProfileID {$profile->ProfileID}: {$profile->identity_count} identidades\n";
}

// 4. ANÁLISIS DE CALIDAD DE ALIASES
echo "\n4. Análisis de calidad de aliases:\n";
echo "- Total aliases: " . DB::table('aliases')->count() . "\n";
echo "- Aliases primarios: " . DB::table('aliases')->where('Primary_', 1)->count() . "\n";
echo "- Aliases de baja calidad: " . DB::table('aliases')->where('LowQuality', 1)->count() . "\n";
echo "- Aliases de alta calidad: " . DB::table('aliases')->where('LowQuality', 0)->count() . "\n";

// 5. TIPOS DE ALIAS MÁS COMUNES
echo "\n5. Tipos de alias más frecuentes:\n";
$aliasTypes = DB::table('aliases as a')
    ->join('alias_types as at', 'a.AliasTypeID', '=', 'at.ID')
    ->select('at.AliasType', DB::raw('COUNT(*) as count'))
    ->groupBy('at.AliasType')
    ->orderBy('count', 'desc')
    ->get();

foreach ($aliasTypes as $type) {
    echo "- {$type->AliasType}: {$type->count} aliases\n";
}

// 6. RELACIONES MÁS COMUNES
echo "\n6. Tipos de relaciones más frecuentes:\n";
$relationTypes = DB::table('profile_relationships')
    ->select('RelationTypeID', DB::raw('COUNT(*) as count'))
    ->groupBy('RelationTypeID')
    ->orderBy('count', 'desc')
    ->limit(10)
    ->get();

foreach ($relationTypes as $rel) {
    echo "- Tipo de relación {$rel->RelationTypeID}: {$rel->count} relaciones\n";
}

// 7. PERFILES CON MÁS RELACIONES
echo "\n7. Perfiles con más relaciones (nodos centrales):\n";

// Como origen de relaciones
$fromRelations = DB::table('profile_relationships')
    ->select('FromProfileID as ProfileID', DB::raw('COUNT(*) as outgoing_relations'))
    ->groupBy('FromProfileID')
    ->orderBy('outgoing_relations', 'desc')
    ->limit(10)
    ->get();

echo "Perfiles que más relaciones salen (FromProfile):\n";
foreach ($fromRelations as $rel) {
    echo "- ProfileID {$rel->ProfileID}: {$rel->outgoing_relations} relaciones salientes\n";
}

// Como destino de relaciones
$toRelations = DB::table('profile_relationships')
    ->select('ToProfileID as ProfileID', DB::raw('COUNT(*) as incoming_relations'))
    ->groupBy('ToProfileID')
    ->orderBy('incoming_relations', 'desc')
    ->limit(10)
    ->get();

echo "\nPerfiles que más relaciones reciben (ToProfile):\n";
foreach ($toRelations as $rel) {
    echo "- ProfileID {$rel->ProfileID}: {$rel->incoming_relations} relaciones entrantes\n";
}

// 8. BÚSQUEDAS POR ID ESPECÍFICO (ejemplo)
echo "\n8. Información detallada de un perfil específico (ProfileID 36):\n";
$profileInfo = DB::table('profiles as p')
    ->leftJoin('identities as i', 'p.ID', '=', 'i.ProfileID')
    ->leftJoin('aliases as a', 'i.FixedRef', '=', 'a.FixedRef')
    ->leftJoin('name_part_groups as npg', 'i.ID', '=', 'npg.IdentityID')
    ->leftJoin('name_part_types as npt', 'npg.NamePartTypeID', '=', 'npt.ID')
    ->where('p.ID', 36)
    ->select('p.ID as ProfileID', 'p.PartySubTypeID', 'i.ID as IdentityID', 
             'i.Primary_', 'a.Primary_', 'a.LowQuality', 'npt.Description as NamePartType')
    ->get();

if ($profileInfo->isNotEmpty()) {
    foreach ($profileInfo as $info) {
        echo "- ProfileID {$info->ProfileID} (Tipo: {$info->PartySubTypeID})\n";
        echo "  * IdentityID: {$info->IdentityID} (Primaria: " . ($info->Primary_ ? 'Sí' : 'No') . ")\n";
        if ($info->NamePartType) {
            echo "  * Componente: {$info->NamePartType}\n";
        }
        break; // Solo mostrar el primero para no sobrecargar
    }
    
    // Relaciones de este perfil
    $relations = DB::table('profile_relationships')
        ->where('FromProfileID', 36)
        ->orWhere('ToProfileID', 36)
        ->get();
    
    echo "  * Relaciones: " . $relations->count() . "\n";
    
    foreach ($relations->take(3) as $relation) {
        $direction = $relation->FromProfileID == 36 ? 'hacia' : 'desde';
        $otherProfile = $relation->FromProfileID == 36 ? $relation->ToProfileID : $relation->FromProfileID;
        echo "    - {$direction} ProfileID {$otherProfile} (Tipo: {$relation->RelationTypeID})\n";
    }
} else {
    echo "- No se encontró información para ProfileID 36\n";
}

// 9. PAÍSES MÁS REPRESENTADOS
echo "\n9. Países en el dataset:\n";
echo "- Total países: " . DB::table('countries')->count() . "\n";
echo "- Total códigos de área: " . DB::table('area_codes')->count() . "\n";

$countries = DB::table('countries')->orderBy('Name')->limit(10)->get();
echo "Primeros países (alfabéticamente):\n";
foreach ($countries as $country) {
    echo "- {$country->Name} ({$country->ISO2})\n";
}

echo "\n=== TIPOS DE CONSULTAS IMPORTANTES QUE PUEDES HACER ===\n";
echo "1. Búsqueda por ProfileID específico para análisis de compliance\n";
echo "2. Análisis de redes de relaciones para detectar conexiones\n";
echo "3. Filtrado por tipo de entidad (individuos vs entidades)\n";
echo "4. Análisis de calidad de datos (aliases de baja calidad)\n";
echo "5. Detección de perfiles con múltiples identidades\n";
echo "6. Análisis geográfico usando países y códigos de área\n";
echo "7. Seguimiento de relaciones históricas vs actuales\n";
echo "8. Análisis de componentes de nombres para matching\n";
echo "9. Identificación de nodos centrales en redes de relaciones\n";
echo "10. Auditoría de integridad referencial entre tablas\n";

echo "\n=== FIN DEL ANÁLISIS ===\n";
