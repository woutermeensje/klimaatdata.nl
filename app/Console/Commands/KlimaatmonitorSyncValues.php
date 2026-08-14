<?php

namespace App\Console\Commands;

use App\Models\ClimateIndicator;
use App\Models\ClimateRegion;
use App\Services\KlimaatmonitorService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('klimaatmonitor:sync-values {--years=10 : Aantal jaren terug vanaf het meest recente volledige jaar}')]
#[Description('Synchroniseer meetwaarden voor de curated indicatoren, per regio en per jaar')]
class KlimaatmonitorSyncValues extends Command
{
    private const GEO_LEVELS = ['nederland', 'provincie', 'gemeente'];

    public function handle(KlimaatmonitorService $service): int
    {
        $indicators = ClimateIndicator::whereIn('external_code', KlimaatmonitorSyncIndicators::CODES)
            ->pluck('id', 'external_code');

        if ($indicators->isEmpty()) {
            $this->error("Geen indicatoren gevonden. Draai eerst 'klimaatmonitor:sync-indicators'.");

            return self::FAILURE;
        }

        $regionIds = ClimateRegion::pluck('id', 'external_code');

        if ($regionIds->isEmpty()) {
            $this->error("Geen regio's gevonden. Draai eerst 'klimaatmonitor:sync-regions'.");

            return self::FAILURE;
        }

        $yearsBack = (int) $this->option('years');
        $latestYear = (int) date('Y') - 1;
        $years = range($latestYear - $yearsBack + 1, $latestYear);

        $totalUpserted = 0;

        foreach ($indicators as $code => $indicatorId) {
            $supportedLevels = array_intersect(self::GEO_LEVELS, $service->geoLevelsForVariable($code));

            if (empty($supportedLevels)) {
                $this->warn("{$code}: geen ondersteund geo-niveau, overgeslagen.");

                continue;
            }

            foreach ($supportedLevels as $geoLevel) {
                foreach ($years as $year) {
                    try {
                        $rows = $service->valuesForVariable($code, $geoLevel, (string) $year);
                    } catch (\Throwable $e) {
                        continue;
                    }

                    $batch = [];

                    foreach ($rows as $row) {
                        $regionId = $regionIds[$row['ExternalCode']] ?? null;

                        if ($regionId === null || ! is_numeric($row['ValueString'] ?? null)) {
                            continue;
                        }

                        $batch[] = [
                            'climate_indicator_id' => $indicatorId,
                            'climate_region_id' => $regionId,
                            'period' => (string) $year,
                            'period_type' => 'year',
                            'value' => (float) $row['ValueString'],
                            'raw_value' => $row['ValueString'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    if (empty($batch)) {
                        continue;
                    }

                    DB::table('climate_values')->upsert(
                        $batch,
                        ['climate_indicator_id', 'climate_region_id', 'period'],
                        ['value', 'raw_value', 'period_type', 'updated_at']
                    );

                    $totalUpserted += count($batch);
                }
            }

            $this->info("{$code}: klaar ({$supportedLevels[0]}".(count($supportedLevels) > 1 ? ' + '.implode(', ', array_slice($supportedLevels, 1)) : '').').');
        }

        $this->info("Totaal {$totalUpserted} waarden weggeschreven (upsert).");

        return self::SUCCESS;
    }
}
