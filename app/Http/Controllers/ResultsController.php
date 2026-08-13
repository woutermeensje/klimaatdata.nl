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
            'provincialCounts' => $data['provincialCounts'],
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

        foreach ($lowestMunicipalities as &$item) {
            $item['per_1000_inwoners'] = round(($item['aantal'] / $item['inwoners']) * 1000, 2);
        }

        $topMunicipalitiesPerCapita = $topMunicipalities;
        usort($topMunicipalitiesPerCapita, fn ($a, $b) => $b['per_1000_inwoners'] <=> $a['per_1000_inwoners']);
        $topMunicipalitiesPerCapita = array_slice($topMunicipalitiesPerCapita, 0, 10);

        $lowestMunicipalitiesPerCapita = $lowestMunicipalities;
        usort($lowestMunicipalitiesPerCapita, fn ($a, $b) => $a['per_1000_inwoners'] <=> $b['per_1000_inwoners']);
        $lowestMunicipalitiesPerCapita = array_slice($lowestMunicipalitiesPerCapita, 0, 10);

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

        usort($provincialCounts, fn ($a, $b) => $b['per_1000_inwoners'] <=> $a['per_1000_inwoners']);
        $provincialCounts = array_slice($provincialCounts, 0, 12);

        return [
            'topMunicipalities' => $topMunicipalities,
            'lowestMunicipalities' => $lowestMunicipalities,
            'topMunicipalitiesPerCapita' => $topMunicipalitiesPerCapita,
            'lowestMunicipalitiesPerCapita' => $lowestMunicipalitiesPerCapita,
            'yearlyTotals' => $yearlyTotals,
            'yearlyElectricShare' => $yearlyElectricShare,
            'yearlyEvPerInhabitant' => $yearlyEvPerInhabitant,
            'provincialCounts' => $provincialCounts,
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
