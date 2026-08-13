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
            'provincialCounts' => $data['provincialCounts'],
            'largestCities' => $data['largestCities'],
        ]);
    }

    public function csv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = $this->flattenForExport();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, ['categorie', 'plaats', 'aantal', 'inhoud', 'type']);

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
        return [
            'topMunicipalities' => [
                ['rank' => 1, 'gemeente' => 'Amsterdam', 'aantal' => 52740],
                ['rank' => 2, 'gemeente' => 'Rotterdam', 'aantal' => 43210],
                ['rank' => 3, 'gemeente' => 'Utrecht', 'aantal' => 31160],
                ['rank' => 4, 'gemeente' => 'Den Haag', 'aantal' => 28980],
                ['rank' => 5, 'gemeente' => 'Eindhoven', 'aantal' => 25340],
                ['rank' => 6, 'gemeente' => 'Tilburg', 'aantal' => 22180],
                ['rank' => 7, 'gemeente' => 'Groningen', 'aantal' => 20120],
                ['rank' => 8, 'gemeente' => 'Nijmegen', 'aantal' => 18450],
                ['rank' => 9, 'gemeente' => 'Almere', 'aantal' => 17120],
                ['rank' => 10, 'gemeente' => 'Breda', 'aantal' => 16430],
            ],
            'lowestMunicipalities' => [
                ['rank' => 1, 'gemeente' => 'Schiermonnikoog', 'aantal' => 28],
                ['rank' => 2, 'gemeente' => 'Vlieland', 'aantal' => 41],
                ['rank' => 3, 'gemeente' => 'Ameland', 'aantal' => 52],
                ['rank' => 4, 'gemeente' => 'Terschelling', 'aantal' => 76],
                ['rank' => 5, 'gemeente' => 'Noardeast-Fryslân', 'aantal' => 108],
                ['rank' => 6, 'gemeente' => 'Westerveld', 'aantal' => 141],
                ['rank' => 7, 'gemeente' => 'Midden-Drenthe', 'aantal' => 163],
                ['rank' => 8, 'gemeente' => 'Heumen', 'aantal' => 172],
                ['rank' => 9, 'gemeente' => 'Bergen (L)', 'aantal' => 181],
                ['rank' => 10, 'gemeente' => 'Stede Broec', 'aantal' => 193],
            ],
            'provincialCounts' => [
                ['provincie' => 'Noord-Holland', 'aantal' => 286450],
                ['provincie' => 'Zuid-Holland', 'aantal' => 267880],
                ['provincie' => 'Noord-Brabant', 'aantal' => 254130],
                ['provincie' => 'Gelderland', 'aantal' => 174330],
                ['provincie' => 'Utrecht', 'aantal' => 161270],
                ['provincie' => 'Overijssel', 'aantal' => 104110],
                ['provincie' => 'Flevoland', 'aantal' => 89050],
                ['provincie' => 'Limburg', 'aantal' => 86140],
                ['provincie' => 'Friesland', 'aantal' => 63300],
                ['provincie' => 'Groningen', 'aantal' => 56000],
                ['provincie' => 'Drenthe', 'aantal' => 44300],
                ['provincie' => 'Zeeland', 'aantal' => 35300],
            ],
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
            $rows[] = ['Top 10 gemeenten', $row['gemeente'], $row['aantal'], 'Meeste elektrische auto\'s', 'gemeente'];
        }

        foreach ($data['lowestMunicipalities'] as $row) {
            $rows[] = ['Top 10 gemeenten', $row['gemeente'], $row['aantal'], 'Minste elektrische auto\'s', 'gemeente'];
        }

        foreach ($data['provincialCounts'] as $row) {
            $rows[] = ['Provincies', $row['provincie'], $row['aantal'], 'Aantal elektrische auto\'s', 'provincie'];
        }

        foreach ($data['largestCities'] as $row) {
            $rows[] = ['Grootste steden', $row['stad'], $row['bevolking'], 'Bevolking', 'stad'];
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

        $header = ['categorie', 'plaats', 'aantal', 'inhoud', 'type'];
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
