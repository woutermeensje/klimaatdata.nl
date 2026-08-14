<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use ZipArchive;

class ResultsController extends Controller
{
    public function index(): \Illuminate\Contracts\View\View
    {
        $data = $this->dataset();

        return view('results', [
            'topMunicipalities' => $data['topMunicipalities'],
            'lowestMunicipalities' => $data['lowestMunicipalities'],
            'topMunicipalitiesPerCapita' => $data['topMunicipalitiesPerCapita'],
            'lowestMunicipalitiesPerCapita' => $data['lowestMunicipalitiesPerCapita'],
            'topMunicipalitiesEndUser' => $data['topMunicipalitiesEndUser'],
            'vehicleTypesTrend' => $data['vehicleTypesTrend'],
            'vehicleTypesTrendYears' => $data['vehicleTypesTrendYears'],
            'fuelComparisonAbsolute' => $data['fuelComparisonAbsolute'],
            'fuelComparisonPercentage' => $data['fuelComparisonPercentage'],
            'chargingPointsProvincial' => $data['chargingPointsProvincial'],
            'chargingPointsPerEvProvincial' => $data['chargingPointsPerEvProvincial'],
            'chargingPointsTopMunicipalities' => $data['chargingPointsTopMunicipalities'],
            'carsPerHouseholdTrend' => $data['carsPerHouseholdTrend'],
            'provincialCounts' => $data['provincialCounts'],
            'provincialTotals' => $data['provincialTotals'],
            'provincialReadiness' => $data['provincialReadiness'],
            'municipalReadiness' => $data['municipalReadiness'],
            'largestCities' => $data['largestCities'],
            'yearlyTotals' => $data['yearlyTotals'],
            'yearlyElectricShare' => $data['yearlyElectricShare'],
            'yearlyEvPerInhabitant' => $data['yearlyEvPerInhabitant'],
        ]);
    }

    public function csv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = $this->flattenForExport();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, ['categorie', 'plaats', 'aantal', 'per_1000_inwoners', 'inhoud', 'type']);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 'klimaatdata-resultaten.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function excel(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = $this->flattenForExport();
        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_');

        $zip = new ZipArchive();
        $zip->open($tmpFile, ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', $this->xlsxContentTypes());
        $zip->addFromString('_rels/.rels', $this->xlsxRootRelationships());
        $zip->addFromString('docProps/core.xml', $this->xlsxCoreProps());
        $zip->addFromString('docProps/app.xml', $this->xlsxAppProps());
        $zip->addFromString('xl/workbook.xml', $this->xlsxWorkbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->xlsxWorkbookRelations());
        $zip->addFromString('xl/styles.xml', $this->xlsxStyles());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->xlsxWorksheet($rows));
        $zip->close();

        return response()->streamDownload(function () use ($tmpFile) {
            $handle = fopen($tmpFile, 'rb');
            fpassthru($handle);
            fclose($handle);
            unlink($tmpFile);
        }, 'klimaatdata-resultaten.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function dataset(): array
    {
        $topMunicipalities = [
            ['rank' => 1, 'gemeente' => 'Amsterdam', 'aantal' => 43172, 'inwoners' => 931298],
            ['rank' => 2, 'gemeente' => 'Almere', 'aantal' => 41601, 'inwoners' => 226500],
            ['rank' => 3, 'gemeente' => 'Breda', 'aantal' => 38547, 'inwoners' => 188078],
            ['rank' => 4, 'gemeente' => 'Amersfoort', 'aantal' => 31349, 'inwoners' => 161852],
            ['rank' => 5, 'gemeente' => 'Den Haag', 'aantal' => 25211, 'inwoners' => 566221],
            ['rank' => 6, 'gemeente' => 'Rotterdam', 'aantal' => 16814, 'inwoners' => 670610],
            ['rank' => 7, 'gemeente' => 'Utrecht', 'aantal' => 14290, 'inwoners' => 374238],
            ['rank' => 8, 'gemeente' => 'Tilburg', 'aantal' => 13895, 'inwoners' => 229836],
            ['rank' => 9, 'gemeente' => 'Groningen', 'aantal' => 11680, 'inwoners' => 243768],
            ['rank' => 10, 'gemeente' => 'Haarlemmermeer', 'aantal' => 10204, 'inwoners' => 163128],
        ];

        $lowestMunicipalities = [
            ['rank' => 1, 'gemeente' => 'Schiermonnikoog', 'aantal' => 28, 'inwoners' => 930],
            ['rank' => 2, 'gemeente' => 'Vlieland', 'aantal' => 41, 'inwoners' => 1120],
            ['rank' => 3, 'gemeente' => 'Ameland', 'aantal' => 52, 'inwoners' => 3670],
            ['rank' => 4, 'gemeente' => 'Terschelling', 'aantal' => 76, 'inwoners' => 4830],
            ['rank' => 5, 'gemeente' => 'Noardeast-Fryslân', 'aantal' => 108, 'inwoners' => 45400],
            ['rank' => 6, 'gemeente' => 'Westerveld', 'aantal' => 141, 'inwoners' => 19350],
            ['rank' => 7, 'gemeente' => 'Midden-Drenthe', 'aantal' => 163, 'inwoners' => 33900],
            ['rank' => 8, 'gemeente' => 'Heumen', 'aantal' => 172, 'inwoners' => 16400],
            ['rank' => 9, 'gemeente' => 'Bergen (L)', 'aantal' => 181, 'inwoners' => 13800],
            ['rank' => 10, 'gemeente' => 'Stede Broec', 'aantal' => 193, 'inwoners' => 35100],
        ];

        foreach ($topMunicipalities as &$item) {
            $item['per_1000_inwoners'] = round(($item['aantal'] / $item['inwoners']) * 1000, 2);
        }
        unset($item);

        foreach ($lowestMunicipalities as &$item) {
            $item['per_1000_inwoners'] = round(($item['aantal'] / $item['inwoners']) * 1000, 2);
        }
        unset($item);

        $topMunicipalitiesPerCapita = $topMunicipalities;
        usort($topMunicipalitiesPerCapita, fn ($a, $b) => $b['per_1000_inwoners'] <=> $a['per_1000_inwoners']);
        $topMunicipalitiesPerCapita = array_slice($topMunicipalitiesPerCapita, 0, 10);

        $lowestMunicipalitiesPerCapita = $lowestMunicipalities;
        usort($lowestMunicipalitiesPerCapita, fn ($a, $b) => $a['per_1000_inwoners'] <=> $b['per_1000_inwoners']);
        $lowestMunicipalitiesPerCapita = array_slice($lowestMunicipalitiesPerCapita, 0, 10);

        $topMunicipalitiesEndUser = [
            ['rank' => 1, 'gemeente' => 'Amsterdam', 'aantal' => 23164, 'inwoners' => 931298],
            ['rank' => 2, 'gemeente' => 'Rotterdam', 'aantal' => 15872, 'inwoners' => 670610],
            ['rank' => 3, 'gemeente' => 'Den Haag', 'aantal' => 13748, 'inwoners' => 566221],
            ['rank' => 4, 'gemeente' => 'Utrecht', 'aantal' => 12615, 'inwoners' => 374238],
            ['rank' => 5, 'gemeente' => 'Almere', 'aantal' => 8644, 'inwoners' => 226500],
            ['rank' => 6, 'gemeente' => 'Haarlemmermeer', 'aantal' => 7896, 'inwoners' => 163128],
            ['rank' => 7, 'gemeente' => 'Breda', 'aantal' => 7744, 'inwoners' => 188078],
            ['rank' => 8, 'gemeente' => "'s-Hertogenbosch", 'aantal' => 6888, 'inwoners' => 160757],
            ['rank' => 9, 'gemeente' => 'Amersfoort', 'aantal' => 6878, 'inwoners' => 161852],
            ['rank' => 10, 'gemeente' => 'Eindhoven', 'aantal' => 6699, 'inwoners' => 246417],
        ];

        foreach ($topMunicipalitiesEndUser as &$item) {
            $item['per_1000_inwoners'] = round(($item['aantal'] / $item['inwoners']) * 1000, 2);
        }
        unset($item);

        $vehicleTypesTrendYears = [2020, 2021, 2022, 2023, 2024];

        $vehicleTypesTrend = [
            ['type' => 'Batterij-elektrisch (BEV, alle voertuigen)', 'waarden' => [260260, 363089, 482961, 624797, 763399]],
            ['type' => 'Plug-in hybride (PHEV, alle voertuigen)', 'waarden' => [97918, 137743, 187184, 263712, 374982]],
            ['type' => 'Elektrische 2-/3-wielers', 'waarden' => [77590, 105438, 134668, 149067, 157092]],
            ['type' => "Lichte elektrische bedrijfsauto's", 'waarden' => [6006, 9031, 13699, 23555, 35251]],
            ['type' => 'Elektrische lichte vierwielers', 'waarden' => [2766, 3378, 4551, 6753, 9169]],
            ['type' => "Zware elektrische bedrijfsauto's", 'waarden' => [146, 208, 308, 1459, 2134]],
            ['type' => 'Elektrische motorfietsen', 'waarden' => [894, 1063, 1372, 1697, 2067]],
            ['type' => 'Waterstof (FCEV, alle voertuigen)', 'waarden' => [392, 558, 693, 727, 785]],
        ];

        foreach ($vehicleTypesTrend as $index => &$item) {
            $item['rank'] = $index + 1;
        }
        unset($item);

        $fuelComparisonAbsolute = [
            'Groningen' => ['Benzine' => 232515, 'Diesel' => 23605, 'LPG' => 4210, 'CNG' => 125, 'Elektrisch' => 13398, 'PHEV' => 10223, 'Waterstof' => 42, 'Totaal' => 302337],
            'Fryslân' => ['Benzine' => 270736, 'Diesel' => 40659, 'LPG' => 5608, 'CNG' => 173, 'Elektrisch' => 13457, 'PHEV' => 10929, 'Waterstof' => 16, 'Totaal' => 357641],
            'Drenthe' => ['Benzine' => 218617, 'Diesel' => 26182, 'LPG' => 4482, 'CNG' => 105, 'Elektrisch' => 10831, 'PHEV' => 9829, 'Waterstof' => 30, 'Totaal' => 286465],
            'Overijssel' => ['Benzine' => 488859, 'Diesel' => 42779, 'LPG' => 7412, 'CNG' => 163, 'Elektrisch' => 25522, 'PHEV' => 22392, 'Waterstof' => 8, 'Totaal' => 625567],
            'Flevoland' => ['Benzine' => 183150, 'Diesel' => 17417, 'LPG' => 3235, 'CNG' => 36, 'Elektrisch' => 42635, 'PHEV' => 15638, 'Waterstof' => 11, 'Totaal' => 291518],
            'Gelderland' => ['Benzine' => 879014, 'Diesel' => 63275, 'LPG' => 13764, 'CNG' => 203, 'Elektrisch' => 44870, 'PHEV' => 38967, 'Waterstof' => 138, 'Totaal' => 1111465],
            'Utrecht' => ['Benzine' => 537856, 'Diesel' => 36276, 'LPG' => 6725, 'CNG' => 115, 'Elektrisch' => 84001, 'PHEV' => 38151, 'Waterstof' => 101, 'Totaal' => 778766],
            'Noord-Holland' => ['Benzine' => 996700, 'Diesel' => 62542, 'LPG' => 13735, 'CNG' => 297, 'Elektrisch' => 121852, 'PHEV' => 75544, 'Waterstof' => 64, 'Totaal' => 1393453],
            'Zuid-Holland' => ['Benzine' => 1301103, 'Diesel' => 73042, 'LPG' => 13669, 'CNG' => 317, 'Elektrisch' => 80151, 'PHEV' => 68260, 'Waterstof' => 135, 'Totaal' => 1672627],
            'Zeeland' => ['Benzine' => 174235, 'Diesel' => 12091, 'LPG' => 1960, 'CNG' => 23, 'Elektrisch' => 7261, 'PHEV' => 7114, 'Waterstof' => 4, 'Totaal' => 215772],
            'Noord-Brabant' => ['Benzine' => 1128456, 'Diesel' => 77241, 'LPG' => 14302, 'CNG' => 215, 'Elektrisch' => 95480, 'PHEV' => 58714, 'Waterstof' => 66, 'Totaal' => 1479750],
            'Limburg' => ['Benzine' => 505789, 'Diesel' => 24313, 'LPG' => 7955, 'CNG' => 91, 'Elektrisch' => 18084, 'PHEV' => 17981, 'Waterstof' => 17, 'Totaal' => 615243],
        ];

        $fuelComparisonPercentage = [];
        foreach ($fuelComparisonAbsolute as $provincie => $fuels) {
            $totaal = $fuels['Totaal'];
            $row = ['provincie' => $provincie];
            foreach (['Benzine', 'Diesel', 'LPG', 'CNG', 'Elektrisch', 'PHEV', 'Waterstof'] as $label) {
                $row[$label] = round($fuels[$label] / $totaal * 100, 1);
            }
            $fuelComparisonPercentage[] = $row;
        }

        $chargingPointsProvincial = [
            ['provincie' => 'Zuid-Holland', 'aantal' => 189902],
            ['provincie' => 'Noord-Holland', 'aantal' => 166443],
            ['provincie' => 'Noord-Brabant', 'aantal' => 163699],
            ['provincie' => 'Gelderland', 'aantal' => 124639],
            ['provincie' => 'Utrecht', 'aantal' => 94942],
            ['provincie' => 'Overijssel', 'aantal' => 64945],
            ['provincie' => 'Limburg', 'aantal' => 59507],
            ['provincie' => 'Fryslân', 'aantal' => 31995],
            ['provincie' => 'Drenthe', 'aantal' => 27779],
            ['provincie' => 'Zeeland', 'aantal' => 26200],
            ['provincie' => 'Groningen', 'aantal' => 24322],
            ['provincie' => 'Flevoland', 'aantal' => 20978],
        ];

        $chargingPointsTopMunicipalities = [
            ['rank' => 1, 'gemeente' => 'Amsterdam', 'aantal' => 36645],
            ['rank' => 2, 'gemeente' => 'Rotterdam', 'aantal' => 29764],
            ['rank' => 3, 'gemeente' => 'Den Haag', 'aantal' => 23238],
            ['rank' => 4, 'gemeente' => 'Utrecht', 'aantal' => 20999],
            ['rank' => 5, 'gemeente' => 'Haarlemmermeer', 'aantal' => 13916],
            ['rank' => 6, 'gemeente' => 'Eindhoven', 'aantal' => 12146],
            ['rank' => 7, 'gemeente' => 'Breda', 'aantal' => 11806],
            ['rank' => 8, 'gemeente' => 'Tilburg', 'aantal' => 10577],
            ['rank' => 9, 'gemeente' => "'s-Hertogenbosch", 'aantal' => 10508],
            ['rank' => 10, 'gemeente' => 'Apeldoorn', 'aantal' => 10043],
        ];

        $carsPerHouseholdTrend = [
            ['jaar' => 2010, 'aantal' => 1.03],
            ['jaar' => 2011, 'aantal' => 1.04],
            ['jaar' => 2012, 'aantal' => 1.05],
            ['jaar' => 2013, 'aantal' => 1.05],
            ['jaar' => 2014, 'aantal' => 1.05],
            ['jaar' => 2015, 'aantal' => 1.04],
            ['jaar' => 2016, 'aantal' => 1.05],
            ['jaar' => 2017, 'aantal' => 1.06],
            ['jaar' => 2018, 'aantal' => 1.07],
            ['jaar' => 2019, 'aantal' => 1.07],
            ['jaar' => 2020, 'aantal' => 1.07],
            ['jaar' => 2021, 'aantal' => 1.08],
            ['jaar' => 2022, 'aantal' => 1.08],
            ['jaar' => 2023, 'aantal' => 1.08],
            ['jaar' => 2024, 'aantal' => 1.08],
        ];

        $yearlyTotals = [
            ['jaar' => 2015, 'totaal' => 12000],
            ['jaar' => 2016, 'totaal' => 26000],
            ['jaar' => 2017, 'totaal' => 52000],
            ['jaar' => 2018, 'totaal' => 98000],
            ['jaar' => 2019, 'totaal' => 155000],
            ['jaar' => 2020, 'totaal' => 220000],
            ['jaar' => 2021, 'totaal' => 320000],
            ['jaar' => 2022, 'totaal' => 450000],
            ['jaar' => 2023, 'totaal' => 640000],
            ['jaar' => 2024, 'totaal' => 860000],
            ['jaar' => 2025, 'totaal' => 980000],
        ];

        $yearlyElectricShare = [
            ['jaar' => 2015, 'percentage' => 0.5],
            ['jaar' => 2016, 'percentage' => 0.8],
            ['jaar' => 2017, 'percentage' => 1.2],
            ['jaar' => 2018, 'percentage' => 1.9],
            ['jaar' => 2019, 'percentage' => 2.8],
            ['jaar' => 2020, 'percentage' => 4.1],
            ['jaar' => 2021, 'percentage' => 6.2],
            ['jaar' => 2022, 'percentage' => 8.7],
            ['jaar' => 2023, 'percentage' => 11.9],
            ['jaar' => 2024, 'percentage' => 15.4],
            ['jaar' => 2025, 'percentage' => 18.3],
        ];

        $yearlyEvPerInhabitant = [
            ['jaar' => 2015, 'per_1000_inwoners' => 0.4],
            ['jaar' => 2016, 'per_1000_inwoners' => 0.7],
            ['jaar' => 2017, 'per_1000_inwoners' => 1.2],
            ['jaar' => 2018, 'per_1000_inwoners' => 2.1],
            ['jaar' => 2019, 'per_1000_inwoners' => 3.0],
            ['jaar' => 2020, 'per_1000_inwoners' => 4.2],
            ['jaar' => 2021, 'per_1000_inwoners' => 6.0],
            ['jaar' => 2022, 'per_1000_inwoners' => 8.2],
            ['jaar' => 2023, 'per_1000_inwoners' => 11.4],
            ['jaar' => 2024, 'per_1000_inwoners' => 15.2],
            ['jaar' => 2025, 'per_1000_inwoners' => 18.1],
        ];

        $provincialCounts = [
            ['provincie' => 'Noord-Holland', 'aantal' => 286450, 'inwoners' => 2930000],
            ['provincie' => 'Zuid-Holland', 'aantal' => 267880, 'inwoners' => 3871000],
            ['provincie' => 'Noord-Brabant', 'aantal' => 254130, 'inwoners' => 2875000],
            ['provincie' => 'Gelderland', 'aantal' => 174330, 'inwoners' => 2147000],
            ['provincie' => 'Utrecht', 'aantal' => 161270, 'inwoners' => 1500000],
            ['provincie' => 'Overijssel', 'aantal' => 104110, 'inwoners' => 1183000],
            ['provincie' => 'Flevoland', 'aantal' => 89050, 'inwoners' => 440000],
            ['provincie' => 'Limburg', 'aantal' => 86140, 'inwoners' => 1136000],
            ['provincie' => 'Friesland', 'aantal' => 63300, 'inwoners' => 653000],
            ['provincie' => 'Groningen', 'aantal' => 56000, 'inwoners' => 591000],
            ['provincie' => 'Drenthe', 'aantal' => 44300, 'inwoners' => 499000],
            ['provincie' => 'Zeeland', 'aantal' => 35300, 'inwoners' => 390000],
        ];

        foreach ($provincialCounts as &$provincie) {
            $provincie['per_1000_inwoners'] = round(($provincie['aantal'] / $provincie['inwoners']) * 1000, 2);
        }
        unset($provincie);

        $provincialTotals = $provincialCounts;
        usort($provincialTotals, fn ($a, $b) => $b['aantal'] <=> $a['aantal']);
        $provincialTotals = array_slice($provincialTotals, 0, 12);

        usort($provincialCounts, fn ($a, $b) => $b['per_1000_inwoners'] <=> $a['per_1000_inwoners']);
        $provincialCounts = array_slice($provincialCounts, 0, 12);

        $evByProvince = [];
        foreach ($provincialCounts as $row) {
            $evByProvince[$row['provincie']] = (float) $row['aantal'];
        }

        $chargingPointsPerEvProvincial = [];
        foreach ($chargingPointsProvincial as $entry) {
            $provincie = $entry['provincie'];
            $evs = $evByProvince[$provincie] ?? 0;
            $chargingPointsPerEvProvincial[] = [
                'provincie' => $provincie,
                'aantal_ev' => $evs,
                'aantal_laadpunten' => $entry['aantal'],
                'laadpunten_per_ev' => $evs > 0 ? round($entry['aantal'] / $evs, 3) : 0,
            ];
        }

        usort($chargingPointsPerEvProvincial, fn ($a, $b) => $b['laadpunten_per_ev'] <=> $a['laadpunten_per_ev']);

        $provincialReadiness = [
            [
                'provincie' => 'Flevoland',
                'score' => 92.1,
                'ev_per_1000' => 202.39,
                'laadpunten_per_1000' => 18.4,
                'groei_2015_2025' => 164.0,
                'totaal_ev' => 89050,
            ],
            [
                'provincie' => 'Utrecht',
                'score' => 88.7,
                'ev_per_1000' => 107.51,
                'laadpunten_per_1000' => 15.9,
                'groei_2015_2025' => 151.0,
                'totaal_ev' => 161270,
            ],
            [
                'provincie' => 'Noord-Holland',
                'score' => 86.4,
                'ev_per_1000' => 97.76,
                'laadpunten_per_1000' => 14.7,
                'groei_2015_2025' => 142.0,
                'totaal_ev' => 286450,
            ],
        ];

        $municipalReadiness = [
            [
                'gemeente' => 'Amsterdam',
                'score' => 94.8,
                'ev_per_1000' => 46.36,
                'laadpunten_per_1000' => 12.8,
                'groei_2015_2025' => 118.0,
                'totaal_ev' => 43172,
            ],
            [
                'gemeente' => 'Almere',
                'score' => 90.6,
                'ev_per_1000' => 183.67,
                'laadpunten_per_1000' => 16.1,
                'groei_2015_2025' => 155.0,
                'totaal_ev' => 41601,
            ],
            [
                'gemeente' => 'Utrecht',
                'score' => 89.3,
                'ev_per_1000' => 38.18,
                'laadpunten_per_1000' => 11.6,
                'groei_2015_2025' => 149.0,
                'totaal_ev' => 14290,
            ],
        ];

        return [
            'topMunicipalities' => $topMunicipalities,
            'lowestMunicipalities' => $lowestMunicipalities,
            'topMunicipalitiesPerCapita' => $topMunicipalitiesPerCapita,
            'lowestMunicipalitiesPerCapita' => $lowestMunicipalitiesPerCapita,
            'topMunicipalitiesEndUser' => $topMunicipalitiesEndUser,
            'vehicleTypesTrend' => $vehicleTypesTrend,
            'vehicleTypesTrendYears' => $vehicleTypesTrendYears,
            'fuelComparisonAbsolute' => $fuelComparisonAbsolute,
            'fuelComparisonPercentage' => $fuelComparisonPercentage,
            'chargingPointsProvincial' => $chargingPointsProvincial,
            'chargingPointsPerEvProvincial' => $chargingPointsPerEvProvincial,
            'chargingPointsTopMunicipalities' => $chargingPointsTopMunicipalities,
            'carsPerHouseholdTrend' => $carsPerHouseholdTrend,
            'yearlyTotals' => $yearlyTotals,
            'yearlyElectricShare' => $yearlyElectricShare,
            'yearlyEvPerInhabitant' => $yearlyEvPerInhabitant,
            'provincialCounts' => $provincialCounts,
            'provincialTotals' => $provincialTotals,
            'provincialReadiness' => $provincialReadiness,
            'municipalReadiness' => $municipalReadiness,
            'largestCities' => [
                ['rank' => 1, 'stad' => 'Amsterdam', 'bevolking' => 921300],
                ['rank' => 2, 'stad' => 'Rotterdam', 'bevolking' => 662000],
                ['rank' => 3, 'stad' => 'Den Haag', 'bevolking' => 564000],
                ['rank' => 4, 'stad' => 'Utrecht', 'bevolking' => 378000],
                ['rank' => 5, 'stad' => 'Eindhoven', 'bevolking' => 244000],
                ['rank' => 6, 'stad' => 'Tilburg', 'bevolking' => 222000],
                ['rank' => 7, 'stad' => 'Almere', 'bevolking' => 219000],
                ['rank' => 8, 'stad' => 'Groningen', 'bevolking' => 236000],
                ['rank' => 9, 'stad' => 'Breda', 'bevolking' => 185000],
                ['rank' => 10, 'stad' => 'Nijmegen', 'bevolking' => 180000],
            ],
        ];
    }

    private function flattenForExport(): array
    {
        $data = $this->dataset();
        $rows = [];

        foreach ($data['topMunicipalities'] as $row) {
            $rows[] = ['Top 10 gemeenten', $row['gemeente'], $row['aantal'], $row['per_1000_inwoners'], 'Meeste elektrische auto\'s (ruw, 2024)', 'gemeente'];
        }

        foreach ($data['lowestMunicipalities'] as $row) {
            $rows[] = ['Top 10 gemeenten', $row['gemeente'], $row['aantal'], $row['per_1000_inwoners'], 'Minste elektrische auto\'s', 'gemeente'];
        }

        foreach ($data['topMunicipalitiesEndUser'] as $row) {
            $rows[] = ['Top 10 gemeenten', $row['gemeente'], $row['aantal'], $row['per_1000_inwoners'], 'Meeste elektrische auto\'s (eindgebruiker, geen lease-vertekening, 2024)', 'gemeente'];
        }

        foreach ($data['vehicleTypesTrend'] as $row) {
            $rows[] = ['Type elektrisch voertuig', $row['type'], end($row['waarden']), null, 'Aantal in 2024 (5-jaars trend: '.implode(',', $row['waarden']).')', 'voertuigtype'];
        }

        foreach ($data['fuelComparisonAbsolute'] as $provincie => $fuels) {
            foreach ($fuels as $brandstof => $aantal) {
                if ($brandstof === 'Totaal') {
                    continue;
                }
                $rows[] = ['Brandstofvergelijking (aantal)', $provincie, $aantal, null, $brandstof, 'provincie'];
            }
        }

        foreach ($data['fuelComparisonPercentage'] as $row) {
            foreach ($row as $brandstof => $waarde) {
                if ($brandstof === 'provincie') {
                    continue;
                }
                $rows[] = ['Brandstofvergelijking (%)', $row['provincie'], $waarde, null, $brandstof, 'provincie'];
            }
        }

        foreach ($data['chargingPointsProvincial'] as $row) {
            $rows[] = ['Laadinfrastructuur', $row['provincie'], $row['aantal'], null, 'Totaal aantal laadpunten', 'provincie'];
        }

        foreach ($data['chargingPointsTopMunicipalities'] as $row) {
            $rows[] = ['Laadinfrastructuur', $row['gemeente'], $row['aantal'], null, 'Totaal aantal laadpunten', 'gemeente'];
        }

        foreach ($data['carsPerHouseholdTrend'] as $row) {
            $rows[] = ['Autobezit', (string) $row['jaar'], $row['aantal'], null, 'Auto\'s per huishouden', 'nederland'];
        }

        foreach ($data['provincialCounts'] as $row) {
            $rows[] = ['Provincies', $row['provincie'], $row['aantal'], null, 'Aantal elektrische auto\'s', 'provincie'];
        }

        foreach ($data['largestCities'] as $row) {
            $rows[] = ['Grootste steden', $row['stad'], $row['bevolking'], null, 'Bevolking', 'stad'];
        }

        return $rows;
    }

    private function xlsxContentTypes(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
XML;
    }

    private function xlsxRootRelationships(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
XML;
    }

    private function xlsxCoreProps(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <dc:creator>Klimaatdata.nl</dc:creator>
  <cp:lastModifiedBy>Klimaatdata.nl</cp:lastModifiedBy>
  <dcterms:created xsi:type="dcterms:W3CDTF">2026-08-13T00:00:00Z</dcterms:created>
  <dcterms:modified xsi:type="dcterms:W3CDTF">2026-08-13T00:00:00Z</dcterms:modified>
</cp:coreProperties>
XML;
    }

    private function xlsxAppProps(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
  <Application>Klimaatdata.nl</Application>
</Properties>
XML;
    }

    private function xlsxWorkbook(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Resultaten" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>
XML;
    }

    private function xlsxWorkbookRelations(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
XML;
    }

    private function xlsxStyles(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>
  <fills count="1"><fill><patternFill patternType="none"/></fill></fills>
  <borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>
  <cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>
</styleSheet>
XML;
    }

    private function xlsxWorksheet(array $rows): string
    {
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml[] = '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $xml[] = '<sheetData>';

        $header = ['categorie', 'plaats', 'aantal', 'per_1000_inwoners', 'inhoud', 'type'];
        $xml[] = '<row r="1">';
        foreach ($header as $index => $value) {
            $col = chr(65 + $index);
            $xml[] = '<c r="' . $col . '1" t="inlineStr"><is><t>' . htmlspecialchars($value) . '</t></is></c>';
        }
        $xml[] = '</row>';

        foreach ($rows as $rowIndex => $row) {
            $r = $rowIndex + 2;
            $xml[] = '<row r="' . $r . '">';
            foreach ($row as $colIndex => $value) {
                $col = chr(65 + $colIndex);
                $xml[] = '<c r="' . $col . $r . '" t="inlineStr"><is><t>' . htmlspecialchars((string) $value) . '</t></is></c>';
            }
            $xml[] = '</row>';
        }

        $xml[] = '</sheetData>';
        $xml[] = '</worksheet>';

        return implode('', $xml);
    }
}
