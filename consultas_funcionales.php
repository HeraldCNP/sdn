<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CONSULTAS IMPORTANTES PARA ANÁLISIS DE SANCIONES ===\n\n";

// 1. ESTADÍSTICAS GENERALES DEL DATASET
echo "1. ESTADÍSTICAS GENERALES:\n";
$stats = [
    'Total perfiles' => DB::table('profiles')->count(),
    'Total individuos (Tipo 4)' => DB::table('profiles')->where('PartySubTypeID', 4)->count(),
    'Total entidades (Tipo 3)' => DB::table('profiles')->where('PartySubTypeID', 3)->count(),
    'Total vessels (Tipo 2)' => DB::table('profiles')->where('PartySubTypeID', 2)->count(),
    'Total identidades' => DB::table('identities')->count(),
    'Total aliases' => DB::table('aliases')->count(),
    'Total relaciones' => DB::table('profile_relationships')->count(),
    'Total países' => DB::table('countries')->count(),
];

foreach ($stats as $key => $value) {
    echo "- {$key}: " . number_format($value) . "\n";
}

// 2. ANÁLISIS DE COMPONENTES DE NOMBRES
echo "\n2. COMPONENTES DE NOMBRES DISPONIBLES:\n";
$namePartTypes = DB::table('name_part_types')->orderBy('ID')->get();
foreach ($namePartTypes as $type) {
    $count = DB::table('name_part_groups')->where('NamePartTypeID', $type->ID)->count();
    echo sprintf("- %-15s (ID: %5d): %s ocurrencias\n", $type->Description, $type->ID, number_format($count));
}

// 3. ANÁLISIS DE CALIDAD DE DATOS
echo "\n3. ANÁLISIS DE CALIDAD DE DATOS:\n";
echo "- Aliases primarios: " . number_format(DB::table('aliases')->where('Primary_', 1)->count()) . "\n";
echo "- Aliases de baja calidad: " . number_format(DB::table('aliases')->where('LowQuality', 1)->count()) . "\n";
echo "- Identidades primarias: " . number_format(DB::table('identities')->where('Primary_', 1)->count()) . "\n";
echo "- Identidades marcadas como falsas: " . number_format(DB::table('identities')->where('False_', 1)->count()) . "\n";

// 4. TIPOS DE RELACIONES MÁS COMUNES
echo "\n4. TIPOS DE RELACIONES MÁS FRECUENTES:\n";
$relationTypes = DB::table('profile_relationships')
    ->select('RelationTypeID', DB::raw('COUNT(*) as count'))
    ->groupBy('RelationTypeID')
    ->orderBy('count', 'desc')
    ->get();

foreach ($relationTypes as $rel) {
    echo "- Tipo de relación {$rel->RelationTypeID}: " . number_format($rel->count) . " relaciones\n";
}

// 5. PERFILES MÁS CONECTADOS
echo "\n5. PERFILES CON MÁS RELACIONES (TOP 10):\n";

// Combinar relaciones salientes y entrantes
$topConnected = DB::select("
    SELECT ProfileID, (outgoing + incoming) as total_relations
    FROM (
        SELECT FromProfileID as ProfileID, COUNT(*) as outgoing, 0 as incoming
        FROM profile_relationships 
        GROUP BY FromProfileID
        
        UNION ALL
        
        SELECT ToProfileID as ProfileID, 0 as outgoing, COUNT(*) as incoming
        FROM profile_relationships 
        GROUP BY ToProfileID
    ) combined
    GROUP BY ProfileID
    ORDER BY total_relations DESC
    LIMIT 10
");

foreach ($topConnected as $profile) {
    echo "- ProfileID {$profile->ProfileID}: " . number_format($profile->total_relations) . " relaciones totales\n";
}

// 6. ANÁLISIS DE TIPOS DE ALIAS
echo "\n6. TIPOS DE ALIAS MÁS COMUNES:\n";
$aliasTypeStats = DB::table('aliases')
    ->select('AliasTypeID', DB::raw('COUNT(*) as count'))
    ->groupBy('AliasTypeID')
    ->orderBy('count', 'desc')
    ->get();

foreach ($aliasTypeStats as $type) {
    echo "- Tipo de alias {$type->AliasTypeID}: " . number_format($type->count) . " aliases\n";
}

// 7. MUESTRA DE PAÍSES EN EL DATASET
echo "\n7. PAÍSES EN EL DATASET (primeros 20):\n";
$countries = DB::table('countries')
    ->orderBy('Name')
    ->limit(20)
    ->get();

foreach ($countries as $country) {
    echo "- {$country->Name} ({$country->ISO2})\n";
}

// 8. EJEMPLOS DE PERFILES ESPECÍFICOS
echo "\n8. ANÁLISIS DE PERFILES ESPECÍFICOS:\n";

// Buscar algunos perfiles con relaciones
$sampleProfiles = DB::table('profile_relationships')
    ->select('FromProfileID as ProfileID')
    ->groupBy('FromProfileID')
    ->having(DB::raw('COUNT(*)'), '>', 1)
    ->limit(5)
    ->get();

foreach ($sampleProfiles as $sample) {
    $profileID = $sample->ProfileID;
    
    // Información básica del perfil
    $profile = DB::table('profiles')->where('ID', $profileID)->first();
    if (!$profile) continue;
    
    $tipo = match($profile->PartySubTypeID) {
        4 => 'Individual',
        3 => 'Entidad',
        2 => 'Vessel',
        default => 'Otro'
    };
    
    echo "\nProfileID {$profileID} ({$tipo}):\n";
    
    // Contar componentes de nombre
    $nameComponents = DB::table('name_part_groups as npg')
        ->join('identities as i', 'npg.IdentityID', '=', 'i.ID')
        ->join('name_part_types as npt', 'npg.NamePartTypeID', '=', 'npt.ID')
        ->where('i.ProfileID', $profileID)
        ->select('npt.Description')
        ->distinct()
        ->get();
    
    if ($nameComponents->isNotEmpty()) {
        echo "  - Componentes de nombre: " . $nameComponents->pluck('Description')->join(', ') . "\n";
    }
    
    // Contar relaciones
    $relationCount = DB::table('profile_relationships')
        ->where('FromProfileID', $profileID)
        ->orWhere('ToProfileID', $profileID)
        ->count();
    
    echo "  - Total relaciones: {$relationCount}\n";
    
    // Mostrar algunas relaciones
    $relations = DB::table('profile_relationships')
        ->where('FromProfileID', $profileID)
        ->orWhere('ToProfileID', $profileID)
        ->limit(3)
        ->get();
    
    foreach ($relations as $relation) {
        $direction = $relation->FromProfileID == $profileID ? 'hacia' : 'desde';
        $otherProfile = $relation->FromProfileID == $profileID ? $relation->ToProfileID : $relation->FromProfileID;
        echo "    * {$direction} ProfileID {$otherProfile} (Tipo relación: {$relation->RelationTypeID})\n";
    }
}

echo "\n=== TIPOS DE CONSULTAS ÚTILES PARA COMPLIANCE ===\n";
echo "✓ 1. BÚSQUEDA POR PERFIL ESPECÍFICO:\n";
echo "   SELECT * FROM profiles WHERE ID = [ProfileID];\n\n";

echo "✓ 2. ANÁLISIS DE RED DE RELACIONES:\n";
echo "   SELECT FromProfileID, ToProfileID, RelationTypeID \n";
echo "   FROM profile_relationships WHERE FromProfileID = [ProfileID];\n\n";

echo "✓ 3. FILTRADO POR TIPO DE ENTIDAD:\n";
echo "   SELECT * FROM profiles WHERE PartySubTypeID = 4; -- Individuos\n";
echo "   SELECT * FROM profiles WHERE PartySubTypeID = 3; -- Entidades\n\n";

echo "✓ 4. BÚSQUEDA DE ALIASES DE ALTA CALIDAD:\n";
echo "   SELECT * FROM aliases WHERE LowQuality = 0 AND Primary_ = 1;\n\n";

echo "✓ 5. ANÁLISIS DE COMPONENTES DE NOMBRE:\n";
echo "   SELECT npt.Description, COUNT(*) \n";
echo "   FROM name_part_groups npg \n";
echo "   JOIN name_part_types npt ON npg.NamePartTypeID = npt.ID \n";
echo "   GROUP BY npt.Description;\n\n";

echo "✓ 6. DETECCIÓN DE NODOS CENTRALES:\n";
echo "   SELECT ProfileID, COUNT(*) as connections \n";
echo "   FROM (SELECT FromProfileID as ProfileID FROM profile_relationships \n";
echo "         UNION ALL SELECT ToProfileID FROM profile_relationships) \n";
echo "   GROUP BY ProfileID ORDER BY connections DESC;\n\n";

echo "✓ 7. ANÁLISIS GEOGRÁFICO:\n";
echo "   SELECT c.Name, COUNT(*) FROM countries c \n";
echo "   JOIN area_codes ac ON c.ID = ac.CountryID \n";
echo "   GROUP BY c.Name;\n\n";

echo "✓ 8. AUDITORÍA DE INTEGRIDAD:\n";
echo "   -- Verificar perfiles sin identidades\n";
echo "   SELECT p.ID FROM profiles p \n";
echo "   LEFT JOIN identities i ON p.ID = i.ProfileID \n";
echo "   WHERE i.ProfileID IS NULL;\n\n";

echo "=== FIN DEL ANÁLISIS ===\n";
