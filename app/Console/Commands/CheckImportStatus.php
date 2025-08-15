<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckImportStatus extends Command
{
    protected $signature = 'import:status';
    protected $description = 'Verifica el estado de los datos importados';

    public function handle()
    {
        $this->info('Resumen de datos importados:');
        $this->table(['Tabla', 'Cantidad de registros'], [
            ['DistinctParties', DB::table('distinct_parties')->count()],
            ['Profiles', DB::table('profiles')->count()],
            ['Identities', DB::table('identities')->count()],
            ['DocumentedNames', DB::table('documented_names')->count()],
            ['Aliases', DB::table('aliases')->count()],
            ['NamePartGroups', DB::table('name_part_groups')->count()],
            ['ProfileRelationships', DB::table('profile_relationships')->count()],
            ['AliasTypes', DB::table('alias_types')->count()],
            ['NamePartTypes', DB::table('name_part_types')->count()],
            ['Countries', DB::table('countries')->count()],
            ['AreaCodes', DB::table('area_codes')->count()],
        ]);

        // Mostrar una muestra de ProfileRelationships
        $this->info('Muestra de ProfileRelationships:');
        $relationships = DB::table('profile_relationships')->take(5)->get();
        $this->table(['ID', 'FromProfileID', 'ToProfileID', 'RelationTypeID', 'Former'], 
            $relationships->map(function($r) {
                return [$r->ID, $r->FromProfileID, $r->ToProfileID, $r->RelationTypeID, $r->Former ? 'true' : 'false'];
            })->toArray()
        );

        return Command::SUCCESS;
    }
}
