<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Klimaatdata.nl — Resultaten</title>
    <style>
        :root {
            --bg: #f7f8f6;
            --fg: #1f2937;
            --muted: #6b7280;
            --border: #dfe5e2;
            --card: #ffffff;
            --primary: #0f766e;
            --primary-soft: #dff7f5;
            --warning: #f59e0b;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--fg);
            line-height: 1.5;
        }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 2rem 1.25rem 4rem; }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        h1 { margin: 0; font-size: clamp(2rem, 3vw, 2.7rem); }
        .subtitle { color: var(--muted); margin: .5rem 0 0; }
        .actions {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }
        .btn {
            display: inline-block;
            padding: .7rem 1rem;
            border: 1px solid var(--border);
            background: var(--card);
            color: var(--fg);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
        }
        .btn.primary {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.25rem;
            margin-top: 2rem;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.25rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .card h2 {
            margin: 0 0 1rem;
            font-size: 1.15rem;
        }
        .chart-wrap {
            margin-top: 1rem;
            padding: 1rem 0 .5rem;
        }
        .chart {
            display: flex;
            align-items: flex-end;
            gap: .5rem;
            height: 220px;
            padding: .75rem .25rem 0;
            border-bottom: 1px solid var(--border);
            border-left: 1px solid var(--border);
        }
        .bar-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            min-width: 0;
            height: 100%;
        }
        .bar {
            width: 100%;
            max-width: 30px;
            background: linear-gradient(180deg, #14b8a6 0%, #0f766e 100%);
            border-radius: 6px 6px 0 0;
            min-height: 8px;
        }
        .bar-label {
            margin-top: .6rem;
            font-size: .72rem;
            color: var(--muted);
        }
        .bar-value {
            font-size: .68rem;
            color: var(--muted);
            margin-bottom: .35rem;
        }
        .sparkline-wrap {
            margin-top: 1rem;
            padding: 1rem 0 .5rem;
        }
        .sparkline {
            display: flex;
            align-items: flex-end;
            gap: .5rem;
            height: 220px;
            padding: .75rem .25rem 0;
            border-bottom: 1px solid var(--border);
            border-left: 1px solid var(--border);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .96rem;
        }
        th, td {
            text-align: left;
            padding: .6rem .5rem;
            border-bottom: 1px solid var(--border);
        }
        th {
            color: var(--muted);
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .right { text-align: right; }
        .stats {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        .stat {
            background: var(--primary-soft);
            border-radius: 10px;
            padding: .8rem 1rem;
            min-width: 160px;
            flex: 1;
        }
        .stat strong { display: block; font-size: 1.3rem; }
        .muted { color: var(--muted); }
        @media (max-width: 640px) {
            .topbar { align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="topbar">
            <div>
                <h1>Resultaten</h1>
                <p class="subtitle">Top 10 gemeenten met de meeste en minste elektrische auto’s, provinciale totals en de tien grootste steden van Nederland.</p>
            </div>
            <div class="actions">
                <a class="btn" href="/">Terug naar home</a>
                <a class="btn primary" href="/resultaten/download/csv">Download CSV</a>
                <a class="btn primary" href="/resultaten/download/excel">Download Excel</a>
            </div>
        </div>

        <div class="stats">
            <div class="stat">
                <span class="muted">Meeste EV’s</span>
                <strong>{{ $topMunicipalities[0]['gemeente'] }}</strong>
                <span class="muted">{{ number_format($topMunicipalities[0]['aantal'], 0, ',', '.') }} auto’s</span>
            </div>
            <div class="stat">
                <span class="muted">Minste EV’s</span>
                <strong>{{ $lowestMunicipalities[0]['gemeente'] }}</strong>
                <span class="muted">{{ number_format($lowestMunicipalities[0]['aantal'], 0, ',', '.') }} auto’s</span>
            </div>
            <div class="stat">
                <span class="muted">Provincie met meeste EV’s</span>
                <strong>{{ $provincialCounts[0]['provincie'] }}</strong>
                <span class="muted">{{ number_format($provincialCounts[0]['aantal'], 0, ',', '.') }} auto’s</span>
            </div>
        </div>

        <div class="grid">
            <section class="card">
                <h2>Top 10 gemeenten met de meeste elektrische auto’s (ruw, 2024)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Gemeente</th>
                            <th class="right">Aantal</th>
                            <th class="right">EV per 1.000 inwoners</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topMunicipalities as $entry)
                            <tr>
                                <td>{{ $entry['rank'] }}</td>
                                <td>{{ $entry['gemeente'] }}</td>
                                <td class="right">{{ number_format($entry['aantal'], 0, ',', '.') }}</td>
                                <td class="right">{{ number_format($entry['per_1000_inwoners'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p class="muted" style="font-size:.82rem;margin-top:.75rem;margin-bottom:0;">
                    ⚠️ Ouder-Amstel (53.746 EV op 14.447 inwoners) en Houten (28.259 EV op 50.847 inwoners)
                    zijn uit deze top 10 gehaald: leasemaatschappijen registreren wagenparken op hun
                    vestigingsadres, wat daar tot een onrealistisch hoog aantal leidt.
                </p>
            </section>

            <section class="card">
                <h2>Top 10 gemeenten met de minste elektrische auto’s</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Gemeente</th>
                            <th class="right">Aantal</th>
                            <th class="right">EV per 1.000 inwoners</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lowestMunicipalities as $entry)
                            <tr>
                                <td>{{ $entry['rank'] }}</td>
                                <td>{{ $entry['gemeente'] }}</td>
                                <td class="right">{{ number_format($entry['aantal'], 0, ',', '.') }}</td>
                                <td class="right">{{ number_format($entry['per_1000_inwoners'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        </div>

        <div class="grid" style="margin-top:1.25rem;">
            <section class="card">
                <h2>Top 10 gemeenten met de meeste EV's per 1.000 inwoners</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Gemeente</th>
                            <th class="right">EV per 1.000 inwoners</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topMunicipalitiesPerCapita as $entry)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $entry['gemeente'] }}</td>
                                <td class="right">{{ number_format($entry['per_1000_inwoners'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="card">
                <h2>Top 10 gemeenten met de minste EV's per 1.000 inwoners</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Gemeente</th>
                            <th class="right">EV per 1.000 inwoners</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lowestMunicipalitiesPerCapita as $entry)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $entry['gemeente'] }}</td>
                                <td class="right">{{ number_format($entry['per_1000_inwoners'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        </div>

        <div class="grid" style="margin-top:1.25rem;">
            <section class="card" style="grid-column: 1 / -1;">
                <h2>De correcte top 10 (eindgebruiker, geen lease-vertekening, 2024)</h2>
                <p class="muted" style="margin-top:-.3rem; margin-bottom:0;">
                    Gebaseerd op registratie bij eindgebruiker in plaats van eigenaar — hierdoor
                    vertekenen leasemaatschappijen (zoals bij Ouder-Amstel en Houten in de ruwe tabel
                    hierboven) de cijfers niet meer.
                </p>
                <table style="margin-top:1rem;">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Gemeente</th>
                            <th class="right">Aantal</th>
                            <th class="right">EV per 1.000 inwoners</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topMunicipalitiesEndUser as $entry)
                            <tr>
                                <td>{{ $entry['rank'] }}</td>
                                <td>{{ $entry['gemeente'] }}</td>
                                <td class="right">{{ number_format($entry['aantal'], 0, ',', '.') }}</td>
                                <td class="right">{{ number_format($entry['per_1000_inwoners'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="card" style="grid-column: 1 / -1;">
                <h2>Populairste type elektrisch voertuig (laatste 5 jaar)</h2>
                <p class="muted" style="margin-top:-.3rem; margin-bottom:0;">Aantal geregistreerde voertuigen per type, Nederland totaal, gesorteerd op 2024.</p>
                <table style="margin-top:1rem;">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Type</th>
                            @foreach ($vehicleTypesTrendYears as $year)
                                <th class="right">{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vehicleTypesTrend as $entry)
                            <tr>
                                <td>{{ $entry['rank'] }}</td>
                                <td>{{ $entry['type'] }}</td>
                                @foreach ($entry['waarden'] as $waarde)
                                    <td class="right">{{ number_format($waarde, 0, ',', '.') }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        </div>

        <div class="grid" style="margin-top:1.25rem;">
            <section class="card" style="grid-column: 1 / -1;">
                <h2>Brandstofvergelijking per provincie — aantal (2024)</h2>
                <div style="overflow-x:auto;">
                <table style="margin-top:1rem;">
                    <thead>
                        <tr>
                            <th>Provincie</th>
                            <th class="right">Benzine</th>
                            <th class="right">Diesel</th>
                            <th class="right">LPG</th>
                            <th class="right">CNG</th>
                            <th class="right">Elektrisch</th>
                            <th class="right">PHEV</th>
                            <th class="right">Waterstof</th>
                            <th class="right">Totaal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fuelComparisonAbsolute as $provincie => $fuels)
                            <tr>
                                <td>{{ $provincie }}</td>
                                @foreach (['Benzine','Diesel','LPG','CNG','Elektrisch','PHEV','Waterstof','Totaal'] as $label)
                                    <td class="right">{{ number_format($fuels[$label], 0, ',', '.') }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </section>

            <section class="card" style="grid-column: 1 / -1;">
                <h2>Brandstofvergelijking per provincie — aandeel (%)</h2>
                <div style="overflow-x:auto;">
                <table style="margin-top:1rem;">
                    <thead>
                        <tr>
                            <th>Provincie</th>
                            <th class="right">Benzine</th>
                            <th class="right">Diesel</th>
                            <th class="right">LPG</th>
                            <th class="right">CNG</th>
                            <th class="right">Elektrisch</th>
                            <th class="right">PHEV</th>
                            <th class="right">Waterstof</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fuelComparisonPercentage as $entry)
                            <tr>
                                <td>{{ $entry['provincie'] }}</td>
                                @foreach (['Benzine','Diesel','LPG','CNG','Elektrisch','PHEV','Waterstof'] as $label)
                                    <td class="right">{{ number_format($entry[$label], 1, ',', '.') }}%</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </section>
        </div>

        <div class="grid" style="margin-top:1.25rem;">
            <section class="card">
                <h2>Laadpunten per provincie</h2>
                <p class="muted" style="margin-top:-.3rem; margin-bottom:0;">Totaal aantal laadpunten (publiek, semi-publiek, thuis en werk), 2024.</p>
                <table style="margin-top:1rem;">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Provincie</th>
                            <th class="right">Aantal laadpunten</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($chargingPointsProvincial as $entry)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $entry['provincie'] }}</td>
                                <td class="right">{{ number_format($entry['aantal'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="card">
                <h2>Top 10 gemeenten met de meeste laadpunten</h2>
                <table style="margin-top:1rem;">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Gemeente</th>
                            <th class="right">Aantal laadpunten</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($chargingPointsTopMunicipalities as $entry)
                            <tr>
                                <td>{{ $entry['rank'] }}</td>
                                <td>{{ $entry['gemeente'] }}</td>
                                <td class="right">{{ number_format($entry['aantal'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="card" style="grid-column: 1 / -1;">
                <h2>Laadpunten per elektrische auto per provincie</h2>
                <p class="muted" style="margin-top:-.3rem; margin-bottom:0;">Aantal laadpunten gedeeld door het aantal EV’s, van hoog naar laag.</p>
                <table style="margin-top:1rem;">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Provincie</th>
                            <th class="right">EV’s</th>
                            <th class="right">Laadpunten</th>
                            <th class="right">Laadpunten per EV</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($chargingPointsPerEvProvincial as $entry)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $entry['provincie'] }}</td>
                                <td class="right">{{ number_format($entry['aantal_ev'], 0, ',', '.') }}</td>
                                <td class="right">{{ number_format($entry['aantal_laadpunten'], 0, ',', '.') }}</td>
                                <td class="right">{{ number_format($entry['laadpunten_per_ev'], 3, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        </div>

        <div class="grid" style="margin-top:1.25rem;">
            <section class="card" style="grid-column: 1 / -1;">
                <h2>Auto's per huishouden (2010-2024)</h2>
                <p class="muted" style="margin-top:-.3rem; margin-bottom:0;">Gemiddeld aantal geregistreerde personenauto's per huishouden, Nederland — licht stijgend, geen daling.</p>
                <div class="chart-wrap">
                    <div class="chart">
                        @php
                            $maxCarsPerHousehold = max(array_column($carsPerHouseholdTrend, 'aantal'));
                            $minCarsPerHousehold = min(array_column($carsPerHouseholdTrend, 'aantal'));
                        @endphp
                        @foreach ($carsPerHouseholdTrend as $entry)
                            @php
                                $height = $maxCarsPerHousehold > 0 ? ($entry['aantal'] / $maxCarsPerHousehold) * 100 : 0;
                            @endphp
                            <div class="bar-col">
                                <span class="bar-value">{{ number_format($entry['aantal'], 2, ',', '.') }}</span>
                                <div class="bar" style="height: {{ $height }}%;"></div>
                                <span class="bar-label">{{ $entry['jaar'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <p class="muted" style="font-size:.85rem;">
                    Van {{ number_format($minCarsPerHousehold, 2, ',', '.') }} ({{ $carsPerHouseholdTrend[0]['jaar'] }})
                    naar {{ number_format(end($carsPerHouseholdTrend)['aantal'], 2, ',', '.') }}
                    ({{ end($carsPerHouseholdTrend)['jaar'] }}) — een lichte, gestage stijging, geen daling.
                </p>
            </section>
        </div>

        <div class="grid" style="margin-top:1.25rem;">
            <section class="card" style="grid-column: 1 / -1;">
                <h2>Elektrische auto’s in Nederland (totaal per jaar)</h2>
                <p class="muted" style="margin-top:-.3rem; margin-bottom:0;">Schaal: totaal aantal EV’s, zoveel als beschikbaar in deze dataset voor de afgelopen jaren.</p>
                <div class="chart-wrap">
                    <div class="chart">
                        @php
                            $maxYearlyTotal = max(array_column($yearlyTotals, 'totaal'));
                        @endphp
                        @foreach ($yearlyTotals as $entry)
                            @php
                                $height = $maxYearlyTotal > 0 ? ($entry['totaal'] / $maxYearlyTotal) * 100 : 0;
                            @endphp
                            <div class="bar-col">
                                <span class="bar-value">{{ number_format($entry['totaal'], 0, ',', '.') }}</span>
                                <div class="bar" style="height: {{ $height }}%;"></div>
                                <span class="bar-label">{{ $entry['jaar'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <table style="margin-top:1rem;">
                    <thead>
                        <tr>
                            <th>Jaar</th>
                            <th class="right">Totaal EV’s</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($yearlyTotals as $entry)
                            <tr>
                                <td>{{ $entry['jaar'] }}</td>
                                <td class="right">{{ number_format($entry['totaal'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="card" style="grid-column: 1 / -1;">
                <h2>Aandeel elektrische auto’s van het totale autoverkeer in Nederland</h2>
                <p class="muted" style="margin-top:-.3rem; margin-bottom:0;">Percentage van alle geregistreerde auto’s dat elektrisch is, sinds 2015.</p>
                <div class="sparkline-wrap">
                    <div class="sparkline">
                        @php
                            $maxElectricShare = max(array_column($yearlyElectricShare, 'percentage'));
                        @endphp
                        @foreach ($yearlyElectricShare as $entry)
                            @php
                                $height = $maxElectricShare > 0 ? ($entry['percentage'] / $maxElectricShare) * 100 : 0;
                            @endphp
                            <div class="bar-col">
                                <span class="bar-value">{{ number_format($entry['percentage'], 1, ',', '.') }}%</span>
                                <div class="bar" style="height: {{ $height }}%;"></div>
                                <span class="bar-label">{{ $entry['jaar'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <table style="margin-top:1rem;">
                    <thead>
                        <tr>
                            <th>Jaar</th>
                            <th class="right">Aandeel EV</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($yearlyElectricShare as $entry)
                            <tr>
                                <td>{{ $entry['jaar'] }}</td>
                                <td class="right">{{ number_format($entry['percentage'], 1, ',', '.') }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="card" style="grid-column: 1 / -1;">
                <h2>EV per 1.000 inwoners in Nederland</h2>
                <p class="muted" style="margin-top:-.3rem; margin-bottom:0;">Model van het aantal elektrische auto’s per 1.000 inwoners vanaf 2015.</p>
                <div class="sparkline-wrap">
                    <div class="sparkline">
                        @php
                            $maxPerCapita = max(array_column($yearlyEvPerInhabitant, 'per_1000_inwoners'));
                        @endphp
                        @foreach ($yearlyEvPerInhabitant as $entry)
                            @php
                                $height = $maxPerCapita > 0 ? ($entry['per_1000_inwoners'] / $maxPerCapita) * 100 : 0;
                            @endphp
                            <div class="bar-col">
                                <span class="bar-value">{{ number_format($entry['per_1000_inwoners'], 1, ',', '.') }}</span>
                                <div class="bar" style="height: {{ $height }}%;"></div>
                                <span class="bar-label">{{ $entry['jaar'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <table style="margin-top:1rem;">
                    <thead>
                        <tr>
                            <th>Jaar</th>
                            <th class="right">EV per 1.000 inwoners</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($yearlyEvPerInhabitant as $entry)
                            <tr>
                                <td>{{ $entry['jaar'] }}</td>
                                <td class="right">{{ number_format($entry['per_1000_inwoners'], 1, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="card" style="grid-column: 1 / -1;">
                <h2>Top 3 provincies op EV-score</h2>
                <p class="muted" style="margin-top:-.3rem; margin-bottom:0;">Scoremodel gebaseerd op EV per 1.000 inwoners, laadinfrastructuur, groei en totaal aantal EV's.</p>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Provincie</th>
                            <th class="right">Score</th>
                            <th class="right">EV / 1.000</th>
                            <th class="right">Laadpunten / 1.000</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($provincialReadiness as $entry)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $entry['provincie'] }}</td>
                                <td class="right">{{ number_format($entry['score'], 1, ',', '.') }}</td>
                                <td class="right">{{ number_format($entry['ev_per_1000'], 2, ',', '.') }}</td>
                                <td class="right">{{ number_format($entry['laadpunten_per_1000'], 1, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="card" style="grid-column: 1 / -1;">
                <h2>Top 3 gemeenten op EV-score</h2>
                <p class="muted" style="margin-top:-.3rem; margin-bottom:0;">Scoremodel gebaseerd op EV per 1.000 inwoners, laadinfrastructuur, groei en totaal aantal EV's.</p>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Gemeente</th>
                            <th class="right">Score</th>
                            <th class="right">EV / 1.000</th>
                            <th class="right">Laadpunten / 1.000</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($municipalReadiness as $entry)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $entry['gemeente'] }}</td>
                                <td class="right">{{ number_format($entry['score'], 1, ',', '.') }}</td>
                                <td class="right">{{ number_format($entry['ev_per_1000'], 2, ',', '.') }}</td>
                                <td class="right">{{ number_format($entry['laadpunten_per_1000'], 1, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="card" style="grid-column: 1 / -1;">
                <h2>Top 12 provincies met het hoogste aantal EV's per inwoner</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Provincie</th>
                            <th class="right">EV's</th>
                            <th class="right">EV per 1.000 inwoners</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($provincialCounts as $entry)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $entry['provincie'] }}</td>
                                <td class="right">{{ number_format($entry['aantal'], 0, ',', '.') }}</td>
                                <td class="right">{{ number_format($entry['per_1000_inwoners'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="card">
                <h2>Aantal elektrische auto’s per provincie</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Provincie</th>
                            <th class="right">Aantal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($provincialTotals as $entry)
                            <tr>
                                <td>{{ $entry['provincie'] }}</td>
                                <td class="right">{{ number_format($entry['aantal'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="card">
                <h2>Tien grootste steden van Nederland</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Stad</th>
                            <th class="right">Bevolking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($largestCities as $entry)
                            <tr>
                                <td>{{ $entry['rank'] }}</td>
                                <td>{{ $entry['stad'] }}</td>
                                <td class="right">{{ number_format($entry['bevolking'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        </div>
    </div>
</body>
</html>
