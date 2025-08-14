<?php

namespace App\Console\Commands;

use App\Models\Sdn;
use App\Models\SdnAlias; // <-- Falta esta línea
use App\Models\SdnAddress; // <-- Falta esta línea
use Illuminate\Console\Command;
use XMLReader;
use SimpleXMLElement;
use Throwable;

class ImportSdnList extends Command
{
    protected $signature = 'sdn:import';
    protected $description = 'Imports the OFAC Specially Designated Nationals (SDN) list from the XML file.';

    public function handle(): void
    {
        $this->info('Starting SDN list import...');

        $xmlFile = storage_path('app/sdn_advanced.xml');

        if (!file_exists($xmlFile)) {
            $this->error("El archivo XML no se encuentra en {$xmlFile}. Por favor, descárgalo primero.");
            return;
        }

        $reader = new XMLReader();
        if (!$reader->open($xmlFile)) {
            $this->error('Failed to open XML file.');
            return;
        }

        while ($reader->read()) {
            // El cambio clave: ahora buscamos la etiqueta 'Profile'
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'Profile') {
                try {
                    $node = new SimpleXMLElement($reader->readOuterXml());
                    $this->processProfileEntry($node);
                } catch (Throwable $e) {
                    $this->error("Error processing a Profile entry: " . $e->getMessage());
                }
            }
        }

        $reader->close();

        $this->info('SDN list import finished successfully!');
    }

    private function processProfileEntry(SimpleXMLElement $node): void
    {
        // Mapeo de la nueva estructura del XML

        // UID
        $uid = (int) $node->ID;

        // Nombre (Busca el alias principal)
        $sdnName = '';
        if (isset($node->Identity->Alias)) {
            foreach ($node->Identity->Alias as $alias) {
                if ((string) $alias->attributes()['Primary'] === 'true') {
                    $sdnName = (string) $alias->DocumentedName->DocumentedNamePart->NamePartValue;
                    break;
                }
            }
        }

        // Asume un tipo de entidad
        $sdnType = 'Entity'; // El XML no lo indica de forma simple, lo podemos dejar como genérico

        // Como los fragmentos no tenían estas etiquetas, las asignamos vacías
        $program = '';
        $remarks = '';

        // --- Inserción en la base de datos (con los nombres de variables actualizados)
        $sdn = Sdn::updateOrCreate(
            ['uid' => $uid],
            [
                'sdn_name' => $sdnName,
                'sdn_type' => $sdnType,
                'program' => $program,
                'remarks' => $remarks,
            ]
        );

        // --- Procesamiento de Alias
        if (isset($node->Identity->Alias)) {
            foreach ($node->Identity->Alias as $alias) {
                // Solo insertamos los alias que no sean el nombre principal
                if ((string) $alias->attributes()['Primary'] !== 'true') {
                    $aliasName = (string) $alias->DocumentedName->DocumentedNamePart->NamePartValue;

                    $sdn->aliases()->updateOrCreate(
                        ['whole_name' => $aliasName, 'sdn_id' => $sdn->id],
                        [
                            'alias_type' => (string) $alias->AliasTypeID,
                            'first_name' => null, // No tenemos estos campos en el nuevo XML
                            'last_name' => null,  // No tenemos estos campos en el nuevo XML
                            'whole_name' => $aliasName,
                        ]
                    );
                }
            }
        }

        // --- Procesamiento de Direcciones (la lógica se mantiene, asumiendo que el tag es 'Address')
        if (isset($node->Address)) {
            foreach ($node->Address as $address) {
                $sdn->addresses()->updateOrCreate(
                    [
                        'address_line1' => (string) $address->Street1,
                        'city' => (string) $address->City,
                        'sdn_id' => $sdn->id,
                    ],
                    [
                        'address_line2' => (string) $address->Street2,
                        'address_line3' => (string) $address->Street3,
                        'state' => (string) $address->StateOrProvince,
                        'postal_code' => (string) $address->PostalCode,
                        'country' => (string) $address->Country,
                    ]
                );
            }
        }

        $this->line("Processed Profile with UID: {$uid} - {$sdnName}");
    }
}
