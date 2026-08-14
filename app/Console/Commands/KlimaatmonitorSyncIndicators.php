<?php

namespace App\Console\Commands;

use App\Models\ClimateIndicator;
use App\Services\KlimaatmonitorService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('klimaatmonitor:sync-indicators')]
#[Description('Synchroniseer de curated selectie indicatoren naar de lokale database')]
class KlimaatmonitorSyncIndicators extends Command
{
    /**
     * Curated selectie, opgebouwd tijdens de verkenning van de API:
     * klimaat/energie, hernieuwbare energie, elektrisch vervoer, laadinfrastructuur
     * en brandstofvergelijking per provincie/gemeente.
     */
    public const CODES = [
        'inwoners',
        'co2_totaal_inw',
        'co2go_inw',
        'co2verv_inw',
        'perc_he_combi',
        'perc_he_el_combi',
        'kern323a_zmw_grdak',
        'kern335a_zmw_kldak',
        'wind_turbines',
        'kern331b_gas_wont',
        'elekvoert_bev',
        'elekvoert_phev',
        'elekvoert_fcev',
        'pers_autos_herv_bev',
        'kern341a_evt_pcteig',
        'kern341b_evt_pctgbr',
        'kern342a_lpt_pbreg',
        'kern342b_lpt_pbsnl',
        'kern342c_lpt_spreg',
        'kern342d_lpt_spsnl',
        'ldpnt_totaal',
        'wp_benz',
        'wp_die',
        'wplpg',
        'wpgas',
        'wpelek',
        'wpphev',
        'wpfcev',
        'wp_tot',
        'pauto_hhgem',
    ];

    public function handle(KlimaatmonitorService $service): int
    {
        $count = 0;

        foreach (self::CODES as $code) {
            try {
                $variable = $service->get("Variables('{$code}')");
            } catch (\Throwable $e) {
                $this->error("Kon {$code} niet ophalen: {$e->getMessage()}");

                continue;
            }

            ClimateIndicator::updateOrCreate(
                ['external_code' => $variable['ExternalCode']],
                [
                    'name' => $variable['Name'],
                    'unit' => $variable['Unit'] ?: null,
                    'source' => $variable['Source'] ?: null,
                    'description' => $variable['Description'] ?: null,
                ]
            );
            $count++;
        }

        $this->info("{$count} indicatoren gesynchroniseerd.");

        return self::SUCCESS;
    }
}
