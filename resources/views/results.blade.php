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
                <h2>Top 10 gemeenten met de meeste elektrische auto’s</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Gemeente</th>
                            <th class="right">Aantal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topMunicipalities as $entry)
                            <tr>
                                <td>{{ $entry['rank'] }}</td>
                                <td>{{ $entry['gemeente'] }}</td>
                                <td class="right">{{ number_format($entry['aantal'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="card">
                <h2>Top 10 gemeenten met de minste elektrische auto’s</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Gemeente</th>
                            <th class="right">Aantal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lowestMunicipalities as $entry)
                            <tr>
                                <td>{{ $entry['rank'] }}</td>
                                <td>{{ $entry['gemeente'] }}</td>
                                <td class="right">{{ number_format($entry['aantal'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        </div>

        <div class="grid" style="margin-top:1.25rem;">
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
                        @foreach ($provincialCounts as $entry)
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
