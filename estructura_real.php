<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ESTRUCTURA REAL DE LAS TABLAS ===\n\n";

// Ver estructura de name_part_types
echo "Estructura de name_part_types:\n";
try {
    $columns = DB::select("DESCRIBE name_part_types");
    foreach ($columns as $column) {
        echo "- {$column->Field}: {$column->Type}\n";
    }
    
    echo "\nDatos de name_part_types:\n";
    $types = DB::table('name_part_types')->get();
    foreach ($types as $type) {
        print_r((array)$type);
    }
} catch (Exception $e) {
    echo "Error con name_part_types: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Ver estructura de name_part_groups
echo "Estructura de name_part_groups:\n";
try {
    $columns = DB::select("DESCRIBE name_part_groups");
    foreach ($columns as $column) {
        echo "- {$column->Field}: {$column->Type}\n";
    }
    
    echo "\nPrimeros 5 registros de name_part_groups:\n";
    $groups = DB::table('name_part_groups')->limit(5)->get();
    foreach ($groups as $group) {
        print_r((array)$group);
    }
} catch (Exception $e) {
    echo "Error con name_part_groups: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Consulta básica que sí debería funcionar
echo "Consulta básica - conteos:\n";
echo "- name_part_types: " . DB::table('name_part_types')->count() . " registros\n";
echo "- name_part_groups: " . DB::table('name_part_groups')->count() . " registros\n";

// Intentar JOIN más cuidadoso
echo "\nIntentar JOIN básico:\n";
try {
    $sample = DB::table('name_part_groups as npg')
        ->join('name_part_types as npt', 'npg.NamePartTypeID', '=', 'npt.id')  // Usando 'id' minúscula
        ->select('npg.IdentityID', 'npg.NamePartTypeID', 'npg.NamePartValue')
        ->limit(3)
        ->get();
    
    foreach ($sample as $row) {
        print_r((array)$row);
    }
} catch (Exception $e) {
    echo "Error en JOIN: " . $e->getMessage() . "\n";
    
    // Probar sin JOIN
    echo "\nIntentando sin JOIN:\n";
    $sample = DB::table('name_part_groups')->limit(3)->get();
    foreach ($sample as $row) {
        print_r((array)$row);
    }
}
