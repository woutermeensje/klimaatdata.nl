<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DelftDataController extends Controller
{
    private const CODES = [
        'co2_totaal_inw', 'co2go_inw', 'co2verv_inw', 'perc_he_combi', 'perc_he_el_combi',
        'kern323a_zmw_grdak', 'kern335a_zmw_kldak', 'kern331b_gas_wont', 'wind_turbines',
        'elekvoert_bev', 'elekvoert_phev', 'elekvoert_fcev', 'kern341a_evt_pcteig',
        'kern341b_evt_pctgbr', 'ldpnt_totaal', 'kern342a_lpt_pbreg', 'kern342b_lpt_pbsnl',
        'kern342c_lpt_spreg', 'kern342d_lpt_spsnl', 'wp_tot', 'pauto_hhgem', 'wp_benz',
        'wp_die', 'wplpg', 'wpgas', 'wpelek', 'wpphev', 'wpfcev',
    ];

    private const COMPARISON_CITIES = [
        'Delft', 'Den Haag', 'Rotterdam', 'Leiden', 'Dordrecht', 'Zoetermeer',
        'Gouda', 'Schiedam', 'Vlaardingen', 'Rijswijk', 'Katwijk', 'Alphen aan den Rijn',
    ];

    public function index(): View
    {
        $indicators = DB::table('climate_indicators')->whereIn('external_code', self::CODES)
            ->get(['id', 'external_code', 'name', 'unit'])->keyBy('external_code');
        $delft = DB::table('climate_regions')->where('external_code', 'gemeente_503')->first();
        $southHolland = DB::table('climate_regions')->where('external_code', 'provincie_9')->first();
        $indicatorIds = $indicators->pluck('id');
        $delftValues = $this->latestValues($indicatorIds, $delft?->id);
        $provinceValues = $this->latestValues($indicatorIds, $southHolland?->id);
        $trends = $this->trends($indicatorIds, $delft?->id);

        $cityRegions = DB::table('climate_regions')->where('region_type', 'gemeente')
            ->whereIn('name', self::COMPARISON_CITIES)->get(['id', 'name'])->keyBy('name');
        $comparison = collect(self::COMPARISON_CITIES)->map(function (string $city) use ($cityRegions, $indicatorIds) {
            $values = isset($cityRegions[$city]) ? $this->latestValues($indicatorIds, $cityRegions[$city]->id) : [];

            return [
                'name' => $city,
                'co2' => $values['co2_totaal_inw']['value'] ?? null,
                'renewable' => $values['perc_he_combi']['value'] ?? null,
                'ev_share' => $values['kern341a_evt_pcteig']['value'] ?? null,
            ];
        })->all();

        return view('data.delft', compact(
            'indicators', 'delft', 'southHolland', 'delftValues', 'provinceValues', 'trends', 'comparison'
        ) + ['comparisonYear' => collect($delftValues)->pluck('period')->filter()->sortDesc()->first()]);
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

    private function trends($indicatorIds, ?int $regionId): array
    {
        if ($regionId === null || $indicatorIds->isEmpty()) {
            return [];
        }

        return DB::table('climate_values as values')
            ->join('climate_indicators as indicators', 'indicators.id', '=', 'values.climate_indicator_id')
            ->where('values.climate_region_id', $regionId)
            ->whereIn('indicators.external_code', ['co2_totaal_inw', 'perc_he_combi', 'kern341a_evt_pcteig'])
            ->orderBy('values.period')->get(['indicators.external_code', 'values.period', 'values.value'])
            ->groupBy('external_code')->map(fn ($values) => $values->values()->all())->all();
    }
}