<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class NationalAverageController extends Controller
{
    private const INDICATORS = [
        'CO2-uitstoot' => [
            ['code' => 'co2_totaal_inw', 'label' => 'Totale bekende uitstoot per inwoner'],
            ['code' => 'co2go_inw', 'label' => 'Uitstoot gebouwde omgeving per inwoner'],
            ['code' => 'co2verv_inw', 'label' => 'Uitstoot verkeer en vervoer per inwoner'],
        ],
        'Hernieuwbare energie' => [
            ['code' => 'perc_he_combi', 'label' => 'Aandeel hernieuwbare energie'],
            ['code' => 'perc_he_el_combi', 'label' => 'Aandeel hernieuwbare elektriciteit'],
            ['code' => 'kern335a_zmw_kldak', 'label' => 'Zonnepanelen kleine systemen'],
            ['code' => 'kern323a_zmw_grdak', 'label' => 'Zonnepanelen grote systemen'],
            ['code' => 'wind_turbines', 'label' => 'Aantal windturbines'],
        ],
        'Gebouwde omgeving' => [
            ['code' => 'kern331b_gas_wont', 'label' => 'Gemiddeld aardgasverbruik per woning'],
            ['code' => 'aant_ae_woningen', 'label' => 'Woningen met elektrische verwarming'],
            ['code' => 'aant_warmwoningen', 'label' => 'Woningen met stadsverwarming'],
            ['code' => 'aant_gaswoningen', 'label' => 'Woningen met aardgasverwarming'],
        ],
        'Elektrisch vervoer' => [
            ['code' => 'elekvoert_bev', 'label' => 'Batterij-elektrische voertuigen'],
            ['code' => 'kern341a_evt_pcteig', 'label' => 'Elektrische personenauto’s (% van totaal)'],
            ['code' => 'elekvoert_phev', 'label' => 'Plug-in hybride voertuigen'],
            ['code' => 'elekvoert_fcev', 'label' => 'Waterstofauto’s'],
            ['code' => 'ldpnt_totaal', 'label' => 'Publieke en semi-publieke laadpunten'],
            ['code' => 'kern342a_lpt_pbreg', 'label' => 'Publieke reguliere laadpunten'],
            ['code' => 'kern342b_lpt_pbsnl', 'label' => 'Publieke snellaadpunten'],
            ['code' => 'kern342c_lpt_spreg', 'label' => 'Semi-publieke reguliere laadpunten'],
            ['code' => 'kern342d_lpt_spsnl', 'label' => 'Semi-publieke snellaadpunten'],
        ],
        'Mobiliteit' => [
            ['code' => 'wp_tot', 'label' => 'Personenauto’s totaal'],
            ['code' => 'pauto_hhgem', 'label' => 'Personenauto’s per huishouden'],
            ['code' => 'wp_benz', 'label' => 'Personenauto’s op benzine'],
            ['code' => 'wp_die', 'label' => 'Personenauto’s op diesel'],
            ['code' => 'wplpg', 'label' => 'Personenauto’s op LPG'],
            ['code' => 'wpgas', 'label' => 'Personenauto’s op aardgas (CNG)'],
            ['code' => 'wpelek', 'label' => 'Elektrische personenauto’s'],
            ['code' => 'wpphev', 'label' => 'Plug-in hybride personenauto’s'],
            ['code' => 'wpfcev', 'label' => 'Personenauto’s op waterstof'],
        ],
    ];

    private const CODES = [
        'co2_totaal_inw', 'co2go_inw', 'co2verv_inw', 'perc_he_combi', 'perc_he_el_combi',
        'kern335a_zmw_kldak', 'kern323a_zmw_grdak', 'wind_turbines', 'kern331b_gas_wont',
        'aant_ae_woningen', 'aant_warmwoningen', 'aant_gaswoningen', 'elekvoert_bev',
        'kern341a_evt_pcteig', 'elekvoert_phev', 'elekvoert_fcev', 'ldpnt_totaal',
        'kern342a_lpt_pbreg', 'kern342b_lpt_pbsnl', 'kern342c_lpt_spreg', 'kern342d_lpt_spsnl',
        'wp_tot', 'pauto_hhgem', 'wp_benz', 'wp_die', 'wplpg', 'wpgas', 'wpelek', 'wpphev', 'wpfcev',
    ];

    public function index(): View
    {
        $indicators = DB::table('climate_indicators')->whereIn('external_code', self::CODES)
            ->get(['id', 'external_code', 'unit'])->keyBy('external_code');
        $indicatorIds = $indicators->pluck('id');
        $delft = DB::table('climate_regions')->where('external_code', 'gemeente_503')->first();
        $netherlands = DB::table('climate_regions')->where('external_code', 'nederland_1')->first();

        return view('data.landelijk-gemiddelde', [
            'groups' => self::INDICATORS,
            'indicators' => $indicators,
            'delftValues' => $this->latestValues($indicatorIds, $delft?->id),
            'nationalValues' => $this->latestValues($indicatorIds, $netherlands?->id),
        ]);
    }

    private function latestValues($indicatorIds, ?int $regionId): array
    {
        if ($regionId === null || $indicatorIds->isEmpty()) {
            return [];
        }

        return DB::table('climate_values as values')
            ->join('climate_indicators as indicators', 'indicators.id', '=', 'values.climate_indicator_id')
            ->where('values.climate_region_id', $regionId)->whereIn('values.climate_indicator_id', $indicatorIds)
            ->orderByDesc('values.period')->get(['indicators.external_code', 'values.period', 'values.value'])
            ->groupBy('external_code')->map(fn ($values) => (array) $values->first())->all();
    }
}