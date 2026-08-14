<?php

namespace App\Console\Commands;

use App\Models\ClimateRegion;
use App\Services\KlimaatmonitorService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('klimaatmonitor:sync-regions')]
#[Description('Synchroniseer provincies, gemeenten en Nederland als regio\'s naar de lokale database')]
class KlimaatmonitorSyncRegions extends Command
{
    private const LEVELS = [
        'nederland' => 'nederland',
        'provincie' => 'provincie',
        'gemeente' => 'gemeente',
    ];

    public function handle(KlimaatmonitorService $service): int
    {
        $total = 0;

        foreach (self::LEVELS as $regionType => $geoLevel) {
            $items = $service->geoItemsForLevel($geoLevel);

            foreach ($items as $item) {
                ClimateRegion::updateOrCreate(
                    ['external_code' => $item['ExternalCode']],
                    [
                        'name' => $item['Name'],
                        'region_type' => $regionType,
                    ]
                );
                $total++;
            }

            $this->info(count($items)." regio's gesynchroniseerd voor niveau '{$geoLevel}'.");
        }

        $this->info("Totaal {$total} regio's gesynchroniseerd.");

        return self::SUCCESS;
    }
}
