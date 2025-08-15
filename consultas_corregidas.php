<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CONSULTAS IMPORTANTES PARA ANÁLISIS DE SANCIONES ===\n\n";

// Primero vamos a entender los datos que tenemos
echo "1. Análisis de estructura de datos:\n";
echo "- Total perfiles: " . DB::table('profiles')->count() . "\n";
echo "- Total identidades: " . DB::table('identities')->count() . "\n";
echo "- Total aliases: " . DB::table('aliases')->count() . "\n";
echo "- Total documented_names: " . DB::table('documented_names')->count() . "\n";
echo "- Total name_part_groups: " . DB::table('name_part_groups')->count() . "\n";
echo "- Total profile_relationships: " . DB::table('profile_relationships')->count() . "\n\n";

// 2. Ver algunos datos de name_part_groups que contienen los nombres
echo "2. Muestra de name_part_groups (donde están los componentes de nombres):\n";
$nameparts = DB::table('name_part_groups as npg')
    ->join('name_part_types as npt', 'npg.NamePartTypeID', '=', 'npt.ID')
    ->select('npg.IdentityID', 'npt.NamePartType', 'npg.NamePartValue')
    ->limit(10)
    ->get();

foreach ($nameparts as $part) {
    echo "- IdentityID {$part->IdentityID}: {$part->NamePartType} = '{$part->NamePartValue}'\n";
}

// 3. Construir nombres completos de personas
echo "\n3. Construcción de nombres completos por identidad:\n";
$nombres_completos = DB::table('name_part_groups as npg')
    ->join('name_part_types as npt', 'npg.NamePartTypeID', '=', 'npt.ID')
    ->join('identities as i', 'npg.IdentityID', '=', 'i.ID')
    ->where('i.Primary_', 1) // Solo identidades primarias
    ->select('npg.IdentityID', 'i.ProfileID', 'npt.NamePartType', 'npg.NamePartValue')
    ->orderBy('npg.IdentityID')
    ->get();

// Agrupar por IdentityID para construir nombres completos
$identidades = [];
foreach ($nombres_completos as $parte) {
    if (!isset($identidades[$parte->IdentityID])) {
        $identidades[$parte->IdentityID] = [
            'ProfileID' => $parte->ProfileID,
            'parts' => []
        ];
    }
    $identidades[$parte->IdentityID]['parts'][$parte->NamePartType] = $parte->NamePartValue;
}

// Mostrar algunos nombres completos construidos
$count = 0;
foreach ($identidades as $identityId => $data) {
    if ($count >= 10) break;
    
    $nombre = '';
    if (isset($data['parts']['Given Name'])) {
        $nombre .= $data['parts']['Given Name'] . ' ';
    }
    if (isset($data['parts']['Last Name'])) {
        $nombre .= $data['parts']['Last Name'];
    }
    if (isset($data['parts']['Entity Name'])) {
        $nombre = $data['parts']['Entity Name'];
    }
    
    echo "- ProfileID {$data['ProfileID']}: " . trim($nombre) . "\n";
    $count++;
}

// 4. Análisis de tipos de partes más comunes
echo "\n4. Tipos de partes de nombres más comunes:\n";
$tipos_partes = DB::table('name_part_groups as npg')
    ->join('name_part_types as npt', 'npg.NamePartTypeID', '=', 'npt.ID')
    ->select('npt.NamePartType', DB::raw('COUNT(*) as cantidad'))
    ->groupBy('npt.NamePartType')
    ->orderBy('cantidad', 'desc')
    ->get();

foreach ($tipos_partes as $tipo) {
    echo "- {$tipo->NamePartType}: {$tipo->cantidad} ocurrencias\n";
}

// 5. Análisis de subtipos de perfiles
echo "\n5. Distribución de tipos de perfiles:\n";
$subtipos = DB::table('profiles')
    ->select('PartySubTypeID', DB::raw('COUNT(*) as cantidad'))
    ->groupBy('PartySubTypeID')
    ->orderBy('cantidad', 'desc')
    ->get();

foreach ($subtipos as $subtipo) {
    $tipo_nombre = match($subtipo->PartySubTypeID) {
        4 => 'Individual',
        3 => 'Entity',
        2 => 'Vessel', 
        default => 'Otro (' . $subtipo->PartySubTypeID . ')'
    };
    echo "- {$tipo_nombre}: {$subtipo->cantidad} perfiles\n";
}

// 6. Análisis de relaciones más comunes
echo "\n6. Tipos de relaciones más frecuentes:\n";
$tipos_relaciones = DB::table('profile_relationships')
    ->select('RelationTypeID', DB::raw('COUNT(*) as cantidad'))
    ->groupBy('RelationTypeID')
    ->orderBy('cantidad', 'desc')
    ->get();

foreach ($tipos_relaciones as $rel) {
    echo "- Tipo de relación {$rel->RelationTypeID}: {$rel->cantidad} relaciones\n";
}

// 7. Búsqueda por valor de nombre específico
echo "\n7. Búsqueda por nombre (ejemplo con 'PUTIN'):\n";
$busqueda_putin = DB::table('name_part_groups as npg')
    ->join('name_part_types as npt', 'npg.NamePartTypeID', '=', 'npt.ID')
    ->join('identities as i', 'npg.IdentityID', '=', 'i.ID')
    ->join('profiles as p', 'i.ProfileID', '=', 'p.ID')
    ->where('npg.NamePartValue', 'LIKE', '%PUTIN%')
    ->select('p.ID as ProfileID', 'npt.NamePartType', 'npg.NamePartValue', 'p.PartySubTypeID')
    ->get();

foreach ($busqueda_putin as $resultado) {
    echo "- ProfileID {$resultado->ProfileID}: {$resultado->NamePartType} = '{$resultado->NamePartValue}'\n";
}

// 8. Estadísticas de calidad de datos
echo "\n8. Estadísticas de calidad de datos:\n";
echo "- Aliases primarios: " . DB::table('aliases')->where('Primary_', 1)->count() . "\n";
echo "- Aliases de baja calidad: " . DB::table('aliases')->where('LowQuality', 1)->count() . "\n";
echo "- Identidades primarias: " . DB::table('identities')->where('Primary_', 1)->count() . "\n";
echo "- Identidades marcadas como falsas: " . DB::table('identities')->where('False_', 1)->count() . "\n";

echo "\n=== FIN DE CONSULTAS ===\n";
