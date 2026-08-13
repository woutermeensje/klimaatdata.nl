<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Klimaatdata.nl') }} — Databronnen overzicht</title>
    <style>
        :root {
            --bg: #fafaf9;
            --fg: #1c1c1a;
            --muted: #6b6a66;
            --border: #e3e3e0;
            --accent: #0f7a4d;
            --accent-bg: #eaf6ef;
            --code-bg: #f1f0ee;
            --table-head: #f5f5f3;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0 1.5rem 4rem;
            background: var(--bg);
            color: var(--fg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.5;
        }
        .wrap { max-width: 920px; margin: 0 auto; }
        header.page-header {
            padding: 2.5rem 0 1.5rem;
            border-bottom: 1px solid var(--border);
            margin-bottom: 2rem;
        }
        h1 { font-size: 1.6rem; margin: 0 0 .4rem; }
        .subtitle { color: var(--muted); font-size: .95rem; }
        .badges { margin-top: 1rem; display: flex; gap: .5rem; flex-wrap: wrap; }
        .badge {
            background: var(--accent-bg);
            color: var(--accent);
            border-radius: 999px;
            padding: .25rem .75rem;
            font-size: .8rem;
            font-weight: 600;
        }
        h2 {
            font-size: 1.15rem;
            margin: 2.5rem 0 .5rem;
            padding-top: .5rem;
            border-top: 1px solid var(--border);
        }
        h2:first-of-type { border-top: none; }
        h3 { font-size: 1rem; margin: 1.5rem 0 .5rem; color: var(--accent); }
        p { color: #333; }
        .note {
            background: var(--accent-bg);
            border-left: 3px solid var(--accent);
            padding: .75rem 1rem;
            border-radius: 0 6px 6px 0;
            font-size: .9rem;
            margin: .75rem 0;
        }
        .warn {
            background: #fff6e5;
            border-left: 3px solid #b7791f;
            padding: .75rem 1rem;
            border-radius: 0 6px 6px 0;
            font-size: .9rem;
            margin: .75rem 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .85rem;
            margin: .5rem 0 1.5rem;
        }
        th, td {
            text-align: left;
            padding: .45rem .6rem;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }
        th { background: var(--table-head); font-weight: 600; }
        code {
            background: var(--code-bg);
            padding: .1rem .35rem;
            border-radius: 4px;
            font-size: .82em;
        }
        .num { text-align: right; white-space: nowrap; }
        .rank-9 { font-weight: 700; color: var(--accent); }
        footer {
            margin-top: 3rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
            color: var(--muted);
            font-size: .8rem;
        }
    </style>
</head>
<body>
<div class="wrap">

    <header class="page-header">
        <h1>Regionale Klimaatmonitor — databronnen overzicht</h1>
        <p class="subtitle">Persoonlijke referentie: wat zit er in de Open Data Service en wat hebben we er al mee gedaan. Lokaal document, niet bedoeld om online te gaan.</p>
        <div class="badges">
            <span class="badge">1.574 variabelen totaal</span>
            <span class="badge">73 kern-indicatoren</span>
            <span class="badge">12 provincies + Nederland</span>
        </div>
    </header>

    <h2>Over deze bron</h2>
    <p>
        API-key ontvangen van Klimaatmonitor (ABF Research), OData v1 op
        <code>https://klimaatmonitor.databank.nl/jiveservices/odata</code>.
        Voorwaarde: geen live koppeling vanuit de applicatie — data moet periodiek lokaal
        gesynchroniseerd worden. Er komt over ~2 weken (medio eind augustus 2026) een v2 met
        gewijzigde structuur; v1 blijft beschikbaar t/m 31 december 2026.
    </p>
    <div class="note">
        <strong>Waardes opvragen (ontdekt via de handleiding):</strong><br>
        <code>Variables('{code}')/GeoLevels('provincie')/PeriodLevels('year')/Periods('2023')/Values</code><br>
        Geografische niveaus beschikbaar: gemeente, provincie, nederland, RES-regio, sub-RES-regio, buurt e.a.
    </div>
    <div class="warn">
        <strong>Ontbreekt in deze dataset:</strong> klimaatadaptatie (hittestress, wateroverlast),
        luchtkwaliteit, fijnstof, stikstof. Deze bron is vrijwel volledig gericht op
        <strong>energietransitie, uitstoot en mobiliteit</strong>.
    </div>

    @php $kernIndicators = array_filter($catalog['variables'], fn($v) => $v['kern']); @endphp

    <h2>Volledige catalogus (automatisch opgehaald op {{ $catalog['generated_at'] }})</h2>
    <p>
        Dit is de complete lijst zoals de API die nu teruggeeft — {{ $catalog['total_count'] }} variabelen,
        waarvan {{ count($kernIndicators) }} kern-indicatoren. Statische momentopname, geen live koppeling
        vanuit deze pagina.
    </p>

    <h3>Kern-indicatoren ({{ count($kernIndicators) }})</h3>
    <table>
        <thead><tr><th>Code</th><th>Naam</th><th>Eenheid</th><th>Periode</th></tr></thead>
        <tbody>
        @foreach ($kernIndicators as $v)
            <tr>
                <td><code>{{ $v['code'] }}</code></td>
                <td>{{ $v['name'] }}</td>
                <td>{{ $v['unit'] ?: '—' }}</td>
                <td>{{ $v['start'] ?: '?' }}–{{ $v['end'] ?: '?' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h3>Geografische niveaus: provincies + Nederland</h3>
    <table>
        <thead><tr><th>Code</th><th>Naam</th></tr></thead>
        <tbody>
        @foreach ($catalog['provinces'] as $p)
            <tr><td><code>{{ $p['code'] }}</code></td><td>{{ $p['name'] }}</td></tr>
        @endforeach
        @foreach ($catalog['nederland'] as $n)
            <tr><td><code>{{ $n['code'] }}</code></td><td>{{ $n['name'] }}</td></tr>
        @endforeach
        </tbody>
    </table>
    <p style="color:var(--muted);font-size:.85rem;margin-top:-1rem">
        Overige geo-niveaus die de API ook aanbiedt (niet hier uitgeklapt): gemeente, buurt, RES-regio, sub-RES-regio.
    </p>

    <h3>Alle {{ $catalog['total_count'] }} variabelen</h3>
    <input
        type="search"
        id="variable-filter"
        placeholder="Zoek op code of naam…"
        style="width:100%;padding:.5rem .75rem;margin-bottom:.75rem;border:1px solid var(--border);border-radius:6px;font-size:.9rem;"
    >
    <p id="variable-filter-count" style="color:var(--muted);font-size:.85rem;margin-top:-.5rem"></p>
    <table id="all-variables-table">
        <thead><tr><th>Code</th><th>Naam</th><th>Eenheid</th><th>Periode</th></tr></thead>
        <tbody>
        @foreach ($catalog['variables'] as $v)
            <tr data-search="{{ strtolower($v['code'].' '.$v['name']) }}">
                <td><code>{{ $v['code'] }}</code></td>
                <td>{{ $v['name'] }}</td>
                <td>{{ $v['unit'] ?: '—' }}</td>
                <td>{{ $v['start'] ?: '?' }}–{{ $v['end'] ?: '?' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <script>
        (function () {
            var input = document.getElementById('variable-filter');
            var rows = document.querySelectorAll('#all-variables-table tbody tr');
            var count = document.getElementById('variable-filter-count');
            function apply() {
                var term = input.value.trim().toLowerCase();
                var visible = 0;
                rows.forEach(function (row) {
                    var match = !term || row.dataset.search.indexOf(term) !== -1;
                    row.style.display = match ? '' : 'none';
                    if (match) visible++;
                });
                count.textContent = visible + ' van ' + rows.length + ' variabelen';
            }
            input.addEventListener('input', apply);
            apply();
        })();
    </script>

    <h2>Al opgehaalde cijfers — provincievergelijking 2023</h2>
    <p>Live opgevraagd ter verkenning (nog niet gesynchroniseerd naar de lokale database).</p>

    <h3>CO₂-uitstoot per inwoner (ton/inwoner) — <code>co2_totaal_inw</code></h3>
    <table>
        <thead><tr><th>Provincie</th><th class="num">Waarde</th></tr></thead>
        <tbody>
        <tr><td>Utrecht</td><td class="num">3,8</td></tr>
        <tr><td>Overijssel</td><td class="num">4,3</td></tr>
        <tr><td>Noord-Holland</td><td class="num">4,4</td></tr>
        <tr><td>Fryslân</td><td class="num">4,8</td></tr>
        <tr><td>Drenthe</td><td class="num">4,9</td></tr>
        <tr><td>Gelderland</td><td class="num">4,9</td></tr>
        <tr><td>Noord-Brabant</td><td class="num">5,3</td></tr>
        <tr class="rank-9"><td>Zuid-Holland</td><td class="num">5,5</td></tr>
        <tr><td>Groningen</td><td class="num">5,8</td></tr>
        <tr><td>Limburg</td><td class="num">6,6</td></tr>
        <tr><td colspan="2" style="color:var(--muted)">Flevoland en Zeeland: geen waarde beschikbaar voor 2023</td></tr>
        </tbody>
    </table>
    <p style="color:var(--muted);font-size:.85rem;margin-top:-1rem">Gemiddelde (10 provincies): 5,03 ton/inwoner. Zuid-Holland: 8e van 10, ~9% boven gemiddelde.</p>

    <h3>Aandeel hernieuwbare energie (%) — <code>perc_he_combi</code></h3>
    <table>
        <thead><tr><th>Provincie</th><th class="num">Waarde</th></tr></thead>
        <tbody>
        <tr><td>Groningen</td><td class="num">30,4%</td></tr>
        <tr><td>Fryslân</td><td class="num">27,4%</td></tr>
        <tr><td>Drenthe</td><td class="num">26,5%</td></tr>
        <tr><td>Overijssel</td><td class="num">16,3%</td></tr>
        <tr><td>Noord-Brabant</td><td class="num">13,9%</td></tr>
        <tr><td>Noord-Holland</td><td class="num">13,4%</td></tr>
        <tr><td>Gelderland</td><td class="num">12,7%</td></tr>
        <tr><td>Utrecht</td><td class="num">11,9%</td></tr>
        <tr><td>Limburg</td><td class="num">9,3%</td></tr>
        <tr class="rank-9"><td>Zuid-Holland</td><td class="num">8,8%</td></tr>
        <tr><td colspan="2" style="color:var(--muted)">Flevoland en Zeeland: geen waarde beschikbaar voor 2023</td></tr>
        </tbody>
    </table>
    <p style="color:var(--muted);font-size:.85rem;margin-top:-1rem">
        Gemiddelde: 17,1%. Zuid-Holland: laagste van alle 12 provincies — vermoedelijk mede door de
        omvang van de industriële/havengebonden energieconsumptie (Rotterdam) die de noemer opdrijft.
    </p>

    <h2>Indicatorcatalogus per thema</h2>
    <p>Representatieve voorbeelden per thema (niet uitputtend — elk thema bevat meer varianten, o.a. per sector en per broeikasgas).</p>

    <h3>Energie &amp; CO₂ (algemeen)</h3>
    <table>
        <thead><tr><th>Code</th><th>Naam</th><th>Eenheid</th><th>Periode</th></tr></thead>
        <tbody>
        <tr><td><code>kern111a_co2_tot</code></td><td>Totale CO₂-uitstoot (aardgas, elektr., stadswarmte, brandstoffen)</td><td>kton</td><td>—</td></tr>
        <tr><td><code>co2_totaal_inw</code></td><td>Totale CO₂-uitstoot per inwoner</td><td>ton/inwoner</td><td>2010-2024</td></tr>
        <tr><td><code>co2_totaal_combi_kton</code></td><td>Totaal bekende CO₂-uitstoot (aardgas, elektr., stadswarmte, voertuigbrandstoffen)</td><td>kton</td><td>2010-2024</td></tr>
        <tr><td><code>co2go_inw</code></td><td>CO₂-uitstoot gebouwde omgeving per inwoner</td><td>ton/inwoner</td><td>2010-2024</td></tr>
        <tr><td><code>co2verv_inw</code></td><td>CO₂-uitstoot verkeer en vervoer per inwoner</td><td>ton/inwoner</td><td>—</td></tr>
        <tr><td><code>co2ind_inw</code></td><td>CO₂-uitstoot industrie per inwoner</td><td>ton/inwoner</td><td>—</td></tr>
        </tbody>
    </table>

    <h3>Hernieuwbare energie</h3>
    <table>
        <thead><tr><th>Code</th><th>Naam</th><th>Eenheid</th><th>Periode</th></tr></thead>
        <tbody>
        <tr><td><code>perc_he_combi</code></td><td>Aandeel hernieuwbare energie t.o.v. totaal verbruik</td><td>%</td><td>2010-2024</td></tr>
        <tr><td><code>perc_he_el_combi</code></td><td>Aandeel hernieuwbare elektriciteit</td><td>%</td><td>—</td></tr>
        <tr><td><code>hern_tot_inw</code></td><td>Totaal bekende hernieuwbare energie per inwoner</td><td>GJ/inwoner</td><td>—</td></tr>
        <tr><td><code>kern323a_zmw_grdak</code></td><td>Vermogen zonnepanelen (dakopstelling, grote systemen)</td><td>MW</td><td>2018-2024</td></tr>
        <tr><td><code>kern335a_zmw_kldak</code></td><td>Vermogen zonnepanelen (systemen ≤15 kW)</td><td>MW</td><td>2018-2025</td></tr>
        <tr><td><code>wind_turbines</code></td><td>Aantal windturbines</td><td>aantal</td><td>2001-2026</td></tr>
        <tr><td><code>biogas_totaal_tj</code></td><td>Biogas totaal bekende hernieuwbare energie</td><td>TJ</td><td>1990-2024</td></tr>
        </tbody>
    </table>

    <h3>Gebouwde omgeving &amp; warmte</h3>
    <table>
        <thead><tr><th>Code</th><th>Naam</th><th>Eenheid</th><th>Periode</th></tr></thead>
        <tbody>
        <tr><td><code>kern331b_gas_wont</code></td><td>Gemiddeld aardgasverbruik alle woningen (weergecorrigeerd)</td><td>m³</td><td>—</td></tr>
        <tr><td><code>geblabatot</code></td><td>Utiliteitsgebouwen met geldig energielabel A t/m A+++++</td><td>aantal</td><td>2008-2026</td></tr>
        <tr><td><code>sah_warmtenet</code></td><td>Gesubsidieerde aansluitingen op een warmtenet, totaal</td><td>aantal</td><td>2020-2025</td></tr>
        <tr><td><code>kb_go_reg_a_type_dp_wrmpmp</code></td><td>Jaarlijks aantal gesubsidieerde warmtepompen</td><td>aantal</td><td>2016-2025</td></tr>
        </tbody>
    </table>

    <h3>Mobiliteit</h3>
    <table>
        <thead><tr><th>Code</th><th>Naam</th><th>Eenheid</th><th>Periode</th></tr></thead>
        <tbody>
        <tr><td><code>kern341a_evt_pcteig</code></td><td>Percentage elektrische personenauto's (BEV)</td><td>%</td><td>2018-2026</td></tr>
        <tr><td><code>elekvoert_bev</code></td><td>Batterij-elektrische voertuigen totaal, geregistreerd bij eigenaar</td><td>aantal</td><td>2012-2026</td></tr>
        <tr><td><code>kern342a_lpt_pbreg</code></td><td>Publieke reguliere laadpunten</td><td>aantal</td><td>2010-2026</td></tr>
        <tr><td><code>kern342b_lpt_pbsnl</code></td><td>Publieke snellaadpunten</td><td>aantal</td><td>2010-2026</td></tr>
        <tr><td><code>kern342c_lpt_spreg</code></td><td>Semi-publieke reguliere laadpunten</td><td>aantal</td><td>2010-2026</td></tr>
        <tr><td><code>kern342d_lpt_spsnl</code></td><td>Semi-publieke snellaadpunten</td><td>aantal</td><td>2010-2026</td></tr>
        <tr><td><code>ms_fiets</code></td><td>Modal split gereisde km's fiets</td><td>%</td><td style="color:#b7791f">2010-2017 (verouderd)</td></tr>
        <tr><td><code>ovinfiets</code></td><td>Gereisde kilometers fiets</td><td>miljard km</td><td style="color:#b7791f">2010-2017 (verouderd)</td></tr>
        </tbody>
    </table>

    <h3>Landbouw &amp; industrie</h3>
    <table>
        <thead><tr><th>Code</th><th>Naam</th><th>Eenheid</th><th>Periode</th></tr></thead>
        <tbody>
        <tr><td><code>co2landb_inw</code></td><td>CO₂-uitstoot landbouw, bosbouw en visserij per inwoner</td><td>ton/inwoner</td><td>2010-2024</td></tr>
        <tr><td><code>energie_landbouw_inw</code></td><td>Energieverbruik landbouw per inwoner</td><td>GJ/inwoner</td><td>2010-2024</td></tr>
        </tbody>
    </table>

    <h3>Afval / circulair</h3>
    <table>
        <thead><tr><th>Code</th><th>Naam</th><th>Eenheid</th><th>Periode</th></tr></thead>
        <tbody>
        <tr><td><code>afval_co2_kton</code></td><td>Uitstoot afvalverwijdering — CO₂</td><td>kton</td><td>1990-</td></tr>
        <tr><td><code>afval_co2_stort</code></td><td>Uitstoot afvalverwijdering — storten — CO₂</td><td>ton</td><td>1990-2024</td></tr>
        </tbody>
    </table>

    <h2>Artikelideeën op basis van deze data</h2>
    <ul>
        <li><strong>CO₂ &amp; hernieuwbaar:</strong> Zuid-Holland presteert op beide indicatoren onder het provinciaal gemiddelde — met de kanttekening dat de industrie/havenregio de cijfers drukt.</li>
        <li><strong>Mobiliteit-mismatch:</strong> laadpunten-dichtheid vs. aandeel elektrische auto's per provincie — mogelijk hoge infra-dekking zonder proportioneel EV-bezit, of andersom. Fiets/OV-cijfers (<code>ms_fiets</code>, <code>ovinfiets</code>) zijn te gedateerd (stoppen bij 2017) om als actueel argument te gebruiken.</li>
        <li><strong>Woningvoorraad:</strong> aardgasverbruik woningen + energielabels + warmtenet-aansluitingen als indicator voor de warmtetransitie in de gebouwde omgeving.</li>
    </ul>

    <footer>
        Bron: Regionale Klimaatmonitor Open Data Service (ABF Research). Cijfers hierboven zijn
        incidentele live-lookups gedaan tijdens ontwikkeling, nog niet uit de lokale database
        (periodieke sync is nog niet gebouwd).
    </footer>

</div>
</body>
</html>
