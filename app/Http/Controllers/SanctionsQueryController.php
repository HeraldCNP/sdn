<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Profile;
use App\Models\Identity;
use App\Models\ProfileRelationship;
use App\Models\NamePartGroup;
use App\Models\NamePartType;
use App\Models\Country;

class SanctionsQueryController extends Controller
{
    public function index()
    {
        return view('sanctions.index');
    }

    // 1. Búsqueda por ProfileID específico
    public function searchProfile(Request $request)
    {
        $results = null;
        $profileId = null;

        if ($request->has('profile_id') && $request->profile_id) {
            $profileId = $request->profile_id;
            
            // Información básica del perfil
            $profile = DB::table('profiles')->where('ID', $profileId)->first();
            
            if ($profile) {
                // Obtener identidades
                $identities = DB::table('identities')
                    ->where('ProfileID', $profileId)
                    ->get();
                
                // Obtener componentes de nombres
                $nameComponents = DB::table('name_part_groups as npg')
                    ->join('identities as i', 'npg.IdentityID', '=', 'i.ID')
                    ->join('name_part_types as npt', 'npg.NamePartTypeID', '=', 'npt.ID')
                    ->where('i.ProfileID', $profileId)
                    ->select('npt.Description as type', 'npt.ID as type_id')
                    ->distinct()
                    ->get();
                
                // Obtener relaciones
                $relations = DB::table('profile_relationships as pr')
                    ->where('pr.FromProfileID', $profileId)
                    ->orWhere('pr.ToProfileID', $profileId)
                    ->select(
                        'pr.FromProfileID',
                        'pr.ToProfileID', 
                        'pr.RelationTypeID',
                        'pr.Former',
                        DB::raw('CASE WHEN pr.FromProfileID = ' . $profileId . ' THEN "Saliente" ELSE "Entrante" END as direction'),
                        DB::raw('CASE WHEN pr.FromProfileID = ' . $profileId . ' THEN pr.ToProfileID ELSE pr.FromProfileID END as connected_profile')
                    )
                    ->get();
                
                $results = [
                    'profile' => $profile,
                    'identities' => $identities,
                    'name_components' => $nameComponents,
                    'relations' => $relations
                ];
            }
        }

        return view('sanctions.search-profile', compact('results', 'profileId'));
    }

    // 2. Análisis de red de relaciones
    public function networkAnalysis(Request $request)
    {
        $results = null;
        $analysisType = $request->get('analysis_type', 'most_connected');

        switch ($analysisType) {
            case 'most_connected':
                // Perfiles con más relaciones (combinando entrantes y salientes)
                $results = DB::select("
                    SELECT 
                        profile_id,
                        SUM(outgoing) as outgoing_relations,
                        SUM(incoming) as incoming_relations,
                        SUM(outgoing + incoming) as total_relations,
                        p.PartySubTypeID
                    FROM (
                        SELECT FromProfileID as profile_id, COUNT(*) as outgoing, 0 as incoming
                        FROM profile_relationships 
                        GROUP BY FromProfileID
                        UNION ALL
                        SELECT ToProfileID as profile_id, 0 as outgoing, COUNT(*) as incoming
                        FROM profile_relationships 
                        GROUP BY ToProfileID
                    ) combined
                    JOIN profiles p ON combined.profile_id = p.ID
                    GROUP BY profile_id, p.PartySubTypeID
                    ORDER BY total_relations DESC
                    LIMIT 20
                ");
                break;
                
            case 'relation_types':
                // Análisis de tipos de relaciones
                $results = DB::table('profile_relationships')
                    ->select('RelationTypeID', DB::raw('COUNT(*) as count'))
                    ->groupBy('RelationTypeID')
                    ->orderBy('count', 'desc')
                    ->get();
                break;
                
            case 'active_vs_former':
                // Relaciones activas vs anteriores
                $results = DB::table('profile_relationships')
                    ->select(
                        DB::raw('CASE WHEN Former = 1 THEN "Anteriores" ELSE "Activas" END as status'),
                        DB::raw('COUNT(*) as count')
                    )
                    ->groupBy('Former')
                    ->get();
                break;
        }

        return view('sanctions.network-analysis', compact('results', 'analysisType'));
    }

    // 3. Análisis de entidades por tipo
    public function entityAnalysis(Request $request)
    {
        $results = null;
        $entityType = $request->get('entity_type', 'all');

        $query = DB::table('profiles as p')
            ->leftJoin('identities as i', 'p.ID', '=', 'i.ProfileID')
            ->leftJoin('name_part_groups as npg', 'i.ID', '=', 'npg.IdentityID')
            ->leftJoin('name_part_types as npt', 'npg.NamePartTypeID', '=', 'npt.ID')
            ->select(
                'p.ID',
                'p.PartySubTypeID',
                DB::raw('GROUP_CONCAT(DISTINCT npt.Description) as name_components'),
                DB::raw('COUNT(DISTINCT npg.ID) as component_count')
            )
            ->groupBy('p.ID', 'p.PartySubTypeID');

        if ($entityType !== 'all') {
            $query->where('p.PartySubTypeID', $entityType);
        }

        $results = $query->orderBy('p.ID')->limit(50)->get();

        // Estadísticas por tipo
        $statistics = DB::table('profiles')
            ->select(
                'PartySubTypeID',
                DB::raw('COUNT(*) as count'),
                DB::raw('CASE 
                    WHEN PartySubTypeID = 4 THEN "Individuos"
                    WHEN PartySubTypeID = 3 THEN "Entidades"
                    WHEN PartySubTypeID = 2 THEN "Vessels"
                    ELSE CONCAT("Tipo ", PartySubTypeID)
                END as type_name')
            )
            ->groupBy('PartySubTypeID')
            ->orderBy('count', 'desc')
            ->get();

        return view('sanctions.entity-analysis', compact('results', 'entityType', 'statistics'));
    }

    // 4. Análisis de componentes de nombres
    public function nameAnalysis(Request $request)
    {
        $results = null;
        $componentType = $request->get('component_type', 'statistics');

        switch ($componentType) {
            case 'statistics':
                // Estadísticas de componentes de nombres
                $results = DB::table('name_part_groups as npg')
                    ->join('name_part_types as npt', 'npg.NamePartTypeID', '=', 'npt.ID')
                    ->select(
                        'npt.ID',
                        'npt.Description',
                        DB::raw('COUNT(*) as count')
                    )
                    ->groupBy('npt.ID', 'npt.Description')
                    ->orderBy('count', 'desc')
                    ->get();
                break;
                
            case 'profiles_by_component':
                // Perfiles agrupados por tipo de componente específico
                $typeId = $request->get('type_id');
                if ($typeId) {
                    $results = DB::table('name_part_groups as npg')
                        ->join('identities as i', 'npg.IdentityID', '=', 'i.ID')
                        ->join('profiles as p', 'i.ProfileID', '=', 'p.ID')
                        ->join('name_part_types as npt', 'npg.NamePartTypeID', '=', 'npt.ID')
                        ->where('npg.NamePartTypeID', $typeId)
                        ->select(
                            'p.ID as ProfileID',
                            'p.PartySubTypeID',
                            'npt.Description as ComponentType'
                        )
                        ->distinct()
                        ->limit(100)
                        ->get();
                }
                break;
        }

        // Lista de tipos de componentes para el select
        $componentTypes = DB::table('name_part_types')
            ->orderBy('Description')
            ->get();

        return view('sanctions.name-analysis', compact('results', 'componentType', 'componentTypes'));
    }

    // 5. Dashboard con estadísticas generales
    public function dashboard()
    {
        $stats = [
            'total_profiles' => DB::table('profiles')->count(),
            'individuals' => DB::table('profiles')->where('PartySubTypeID', 4)->count(),
            'entities' => DB::table('profiles')->where('PartySubTypeID', 3)->count(),
            'vessels' => DB::table('profiles')->where('PartySubTypeID', 2)->count(),
            'total_relations' => DB::table('profile_relationships')->count(),
            'active_relations' => DB::table('profile_relationships')->where('Former', 0)->count(),
            'former_relations' => DB::table('profile_relationships')->where('Former', 1)->count(),
            'total_countries' => DB::table('countries')->count(),
            'total_identities' => DB::table('identities')->count(),
            'total_aliases' => DB::table('aliases')->count(),
        ];

        // Top tipos de relaciones
        $topRelationTypes = DB::table('profile_relationships')
            ->select('RelationTypeID', DB::raw('COUNT(*) as count'))
            ->groupBy('RelationTypeID')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Top componentes de nombres
        $topNameComponents = DB::table('name_part_groups as npg')
            ->join('name_part_types as npt', 'npg.NamePartTypeID', '=', 'npt.ID')
            ->select('npt.Description', DB::raw('COUNT(*) as count'))
            ->groupBy('npt.Description')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return view('sanctions.dashboard', compact('stats', 'topRelationTypes', 'topNameComponents'));
    }
}
