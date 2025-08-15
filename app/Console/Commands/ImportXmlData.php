<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SimpleXMLElement;
use App\Models\AliasType;
use App\Models\AreaCodeType;
use App\Models\CalendarType;
use App\Models\Country;
use App\Models\AreaCode;
use App\Models\DistinctParty;
use App\Models\Profile;
use App\Models\Identity;
use App\Models\DocumentedName;
use App\Models\Alias;
use App\Models\NamePartGroup;
use App\Models\NamePartType;
use App\Models\ProfileRelationship;
use Illuminate\Support\Facades\DB;

class ImportXmlData extends Command
{
    protected $signature = 'import:xml {--file=}';
    protected $description = 'Importa datos desde el archivo XML a la base de datos.';

    public function handle()
    {
        $filePath = $this->option('file') ?: storage_path('app/sdn_advanced.xml');

        if (!file_exists($filePath)) {
            $this->error('El archivo XML no se encuentra en la ruta especificada.');
            return Command::FAILURE;
        }

        $xml = simplexml_load_file($filePath);

        $this->info('Iniciando la importación de datos desde el XML...');

        DB::transaction(function () use ($xml) {
            $this->importReferenceValues($xml->ReferenceValueSets);
            $this->importDistinctParties($xml->DistinctParties);
            $this->importProfileRelationships($xml->ProfileRelationships);
            $this->importSanctions($xml->SanctionsEntries);
        });

        $this->info('Importación de datos finalizada.');
        return Command::SUCCESS;
    }

    protected function importReferenceValues($referenceValues)
    {
        $this->info('Importando tablas de referencia...');

        // AliasType
        foreach ($referenceValues->AliasTypeValues->AliasType as $node) {
            AliasType::create([
                'ID' => (int)$node['ID'],
                'Description' => (string)$node
            ]);
        }

        // AreaCodeType
        foreach ($referenceValues->AreaCodeTypeValues->AreaCodeType as $node) {
            AreaCodeType::create([
                'ID' => (int)$node['ID'],
                'Description' => (string)$node
            ]);
        }

        // CalendarType
        foreach ($referenceValues->CalendarTypeValues->CalendarType as $node) {
            CalendarType::create([
                'ID' => (int)$node['ID'],
                'Description' => (string)$node
            ]);
        }

        // Country
        foreach ($referenceValues->CountryValues->Country as $node) {
            Country::create([
                'ID' => (int)$node['ID'],
                'ISO2' => (string)$node['ISO2'],
                'Name' => (string)$node
            ]);
        }

        // AreaCode
        foreach ($referenceValues->AreaCodeValues->AreaCode as $node) {
            AreaCode::create([
                'ID' => (int)$node['ID'],
                'CountryID' => (int)$node['CountryID'],
                'AreaCodeTypeID' => (int)$node['AreaCodeTypeID'],
                'Description' => (string)$node['Description'],
                'Code' => (string)$node
            ]);
        }

        // NamePartType
        foreach ($referenceValues->NamePartTypeValues->NamePartType as $node) {
            NamePartType::create([
                'ID' => (int)$node['ID'],
                'Description' => (string)$node
            ]);
        }
    }

    protected function importDistinctParties($distinctParties)
    {
        $this->info('Importando DistinctParties y Profiles...');

        foreach ($distinctParties->DistinctParty as $distinctPartyNode) {
            // DistinctParty
            $distinctParty = DistinctParty::create([
                'FixedRef' => (int)$distinctPartyNode['FixedRef'],
                'Comment' => isset($distinctPartyNode->Comment) ? (string)$distinctPartyNode->Comment : null
            ]);

            // Profile
            if (isset($distinctPartyNode->Profile)) {
                $profileNode = $distinctPartyNode->Profile;
                $profile = Profile::create([
                    'ID' => (int)$profileNode['ID'],
                    'PartySubTypeID' => (int)$profileNode['PartySubTypeID'],
                    'FixedRef' => $distinctParty->FixedRef
                ]);

                // Identity
                if (isset($profileNode->Identity)) {
                    foreach ($profileNode->Identity as $identityNode) {
                        $identity = Identity::create([
                            'ID' => (int)$identityNode['ID'],
                            'FixedRef' => (int)$identityNode['FixedRef'],
                            'Primary_' => isset($identityNode['Primary']) ? (bool)$identityNode['Primary'] : null,
                            'False_' => isset($identityNode['False']) ? (bool)$identityNode['False'] : null,
                            'ProfileID' => $profile->ID
                        ]);

                        // Alias (within Identity)
                        if (isset($identityNode->Alias)) {
                            foreach ($identityNode->Alias as $aliasNode) {
                                // DocumentedName (within Alias)
                                if (isset($aliasNode->DocumentedName)) {
                                    $docNameNode = $aliasNode->DocumentedName;
                                    $documentedName = DocumentedName::create([
                                        'ID' => (int)$docNameNode['ID'],
                                        'FixedRef' => (int)$docNameNode['FixedRef'],
                                        'DocNameStatusID' => isset($docNameNode['DocNameStatusID']) ? (int)$docNameNode['DocNameStatusID'] : null,
                                        'AliasID' => null // Will be set after alias creation
                                    ]);

                                    // Create Alias (generate unique ID since XML doesn't provide one)
                                    $aliasId = (int)$docNameNode['ID']; // Use DocumentedName ID as Alias ID
                                    $alias = Alias::create([
                                        'ID' => $aliasId,
                                        'FixedRef' => (int)$aliasNode['FixedRef'],
                                        'AliasTypeID' => (int)$aliasNode['AliasTypeID'],
                                        'Primary_' => isset($aliasNode['Primary']) ? (bool)$aliasNode['Primary'] : null,
                                        'LowQuality' => isset($aliasNode['LowQuality']) ? (bool)$aliasNode['LowQuality'] : null,
                                        'DocumentedNameID' => $documentedName->ID
                                    ]);

                                    // Update DocumentedName with AliasID
                                    $documentedName->update(['AliasID' => $alias->ID]);
                                }
                            }
                        }

                        // NamePartGroups (within Identity)
                        if (isset($identityNode->NamePartGroups)) {
                            foreach ($identityNode->NamePartGroups->MasterNamePartGroup as $masterNamePartGroup) {
                                if (isset($masterNamePartGroup->NamePartGroup)) {
                                    $namePartGroupNode = $masterNamePartGroup->NamePartGroup;
                                    NamePartGroup::create([
                                        'ID' => (int)$namePartGroupNode['ID'],
                                        'NamePartTypeID' => (int)$namePartGroupNode['NamePartTypeID'],
                                        'IdentityID' => $identity->ID
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    protected function importProfileRelationships($profileRelationships)
    {
        $this->info('Importando ProfileRelationships...');

        if (isset($profileRelationships->ProfileRelationship)) {
            foreach ($profileRelationships->ProfileRelationship as $relationshipNode) {
                ProfileRelationship::create([
                    'ID' => (int)$relationshipNode['ID'],
                    'FromProfileID' => (int)$relationshipNode['From-ProfileID'], // Note the hyphen in XML
                    'ToProfileID' => (int)$relationshipNode['To-ProfileID'], // Note the hyphen in XML
                    'RelationTypeID' => (int)$relationshipNode['RelationTypeID'],
                    'RelationQualityID' => (int)$relationshipNode['RelationQualityID'],
                    'Former' => isset($relationshipNode['Former']) ? (bool)$relationshipNode['Former'] : null,
                    'SanctionsEntryID' => (int)$relationshipNode['SanctionsEntryID'],
                    'Comment' => isset($relationshipNode->Comment) ? (string)$relationshipNode->Comment : null
                ]);
            }
        }
    }

    protected function importSanctions($sanctionsEntries)
    {
        $this->info('Importando datos de las sanciones...');

        // SanctionsEntries only contain basic sanctions information
        // and reference ProfileID, they don't contain the actual profile data
        foreach ($sanctionsEntries->SanctionsEntry as $sanctionsEntry) {
            // Basic sanctions entry information
            $sanctionEntryId = (int)$sanctionsEntry['ID'];
            $profileId = isset($sanctionsEntry['ProfileID']) ? (int)$sanctionsEntry['ProfileID'] : null;
            $listId = isset($sanctionsEntry['ListID']) ? (int)$sanctionsEntry['ListID'] : null;

            $this->info("Processing sanctions entry ID: {$sanctionEntryId}, ProfileID: {$profileId}");

            // For now, we'll just log this information since the main data
            // is already imported from DistinctParties section
            // Additional sanctions-specific data (like EntryEvent, SanctionsMeasure)
            // could be processed here if needed for your specific requirements
        }
    }
}
