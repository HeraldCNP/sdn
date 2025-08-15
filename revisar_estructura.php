<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ESTRUCTURA DE TABLAS ===\n\n";

$tables = [
    'documented_names',
    'aliases', 
    'identities',
    'profiles',
    'name_part_groups',
    'profile_relationships'
];

foreach ($tables as $table) {
    echo "Tabla: {$table}\n";
    try {
        $columns = DB::select("DESCRIBE {$table}");
        foreach ($columns as $column) {
            echo "- {$column->Field}: {$column->Type}\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// Ver algunos datos de ejemplo
echo "=== DATOS DE EJEMPLO ===\n\n";

echo "Primeros 3 registros de documented_names:\n";
$sample = DB::table('documented_names')->limit(3)->get();
foreach ($sample as $row) {
    print_r((array)$row);
}

echo "\nPrimeros 3 registros de aliases:\n";
$sample = DB::table('aliases')->limit(3)->get();
foreach ($sample as $row) {
    print_r((array)$row);
}
