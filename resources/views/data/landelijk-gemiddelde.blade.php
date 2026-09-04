<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delft versus landelijk gemiddelde — Klimaatdata.nl</title>
    <style>
        :root { --ink:#17362b; --muted:#65756d; --line:#d8e3dc; --paper:#f4f7f2; --card:#fff; --green:#176b4d; --mint:#def1e4; --orange:#d8793e; --red:#a94c3f; }
        * { box-sizing:border-box; } body { margin:0; background:var(--paper); color:var(--ink); font:16px/1.55 Georgia,'Times New Roman',serif; }
        .wrap { max-width:1160px; margin:auto; padding:28px 22px 70px; } a { color:var(--green); }
        .nav { display:flex; justify-content:space-between; font:600 14px -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; } .nav a { text-decoration:none; }
        .eyebrow { color:var(--orange); font:700 12px -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; letter-spacing:.12em; text-transform:uppercase; }
        h1 { max-width:820px; margin:36px 0 12px; font-size:clamp(2.5rem,6vw,5.4rem); line-height:.98; font-weight:500; letter-spacing:-.03em; } h2 { margin:0 0 14px; font-size:1.5rem; font-weight:500; }
        .intro { max-width:730px; color:var(--muted); font-size:1.12rem; } .hero { padding-bottom:36px; border-bottom:1px solid var(--line); }
        .legend { display:flex; gap:18px; flex-wrap:wrap; margin-top:20px; color:var(--muted); font:13px -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }
        .dot { display:inline-block; width:10px; height:10px; margin-right:6px; border-radius:50%; background:var(--green); } .dot.orange { background:var(--orange); }
        .section { padding-top:42px; } .panel { background:var(--card); border:1px solid var(--line); padding:20px; overflow-x:auto; }
        table { width:100%; min-width:650px; border-collapse:collapse; font:14px -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }
        th,td { padding:12px 9px; border-bottom:1px solid var(--line); text-align:left; vertical-align:top; } th { color:var(--muted); font-size:11px; letter-spacing:.06em; text-transform:uppercase; }
        th:not(:first-child),td:not(:first-child) { text-align:right; } tr:last-child td { border-bottom:0; } .delft { background:var(--mint); font-weight:600; } .muted { color:var(--muted); }
        .delta.up { color:var(--green); } .delta.down { color:var(--red); } .missing { color:var(--muted); font-style:italic; font-weight:400; }
        .callout { margin-top:16px; padding:15px 17px; background:var(--mint); } .small { color:var(--muted); font:13px/1.5 -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }
        @media(max-width:700px) { .wrap { padding-inline:16px; } h1 { font-size:3.3rem; } .panel { padding:12px; } }
    </style>
</head>
<body>
<main class="wrap">
    <nav class="nav"><a href="/">Klimaatdata.nl</a><a href="/data/delft">Delft-profiel</a></nav>
    <header class="hero">
        <div class="eyebrow">Delft · landelijke vergelijking</div>
        <h1>Delft naast het landelijk gemiddelde</h1>
        <p class="intro">Hoe scoort Delft ten opzichte van Nederland? Per beschikbare indicator staan de meest recente lokale waarde, de landelijke waarde, het meetjaar en het verschil naast elkaar.</p>
        <div class="legend"><span><i class="dot"></i>Delft ligt hoger dan Nederland</span><span><i class="dot orange"></i>Niet beschikbaar in de lokale database</span></div>
    </header>

    @foreach ($groups as $group => $items)
        <section class="section">
            <div class="eyebrow">{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }} · vergelijking</div>
            <h2>{{ $group }}</h2>
            <div class="panel"><table>
                <thead><tr><th>Indicator</th><th>Delft</th><th>Nederland</th><th>Verschil</th><th>Jaar</th></tr></thead>
                <tbody>
                @foreach ($items as $item)
                    @php
                        $delft = $delftValues[$item['code']] ?? null;
                        $national = $nationalValues[$item['code']] ?? null;
                        $unit = $indicators[$item['code']]->unit ?? '';
                        $isPercentage = $unit === '%';
                        $delta = $delft && $national ? (float) $delft['value'] - (float) $national['value'] : null;
                    @endphp
                    <tr class="{{ $delft ? '' : 'muted' }}">
                        <td><strong>{{ $item['label'] }}</strong><br><span class="small">{{ $item['code'] }}{{ $unit ? ' · ' . $unit : '' }}</span></td>
                        <td class="{{ $delft ? 'delft' : 'missing' }}">{{ $delft ? number_format((float) $delft['value'], 2, ',', '.') . ' ' . $unit : 'Niet gesynchroniseerd' }}</td>
                        <td class="{{ $national ? '' : 'missing' }}">{{ $national ? number_format((float) $national['value'], 2, ',', '.') . ' ' . $unit : 'Niet beschikbaar' }}</td>
                        <td class="{{ $delta !== null ? 'delta ' . ($delta >= 0 ? 'up' : 'down') : 'missing' }}">{{ $delta !== null ? ($delta >= 0 ? '+' : '') . number_format($delta, 2, ',', '.') . ($isPercentage ? ' procentpunt' : ' ' . $unit) : '—' }}</td>
                        <td class="small">{{ $delft['period'] ?? $national['period'] ?? '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
        </section>
    @endforeach

    <section class="section"><div class="callout"><strong>Hoe lees je dit?</strong> Nederland is de landelijke regio uit de Klimaatmonitor-database. Bij percentages is het verschil uitgedrukt in procentpunten. Een hoger percentage elektrische auto’s is bijvoorbeeld gunstig; bij CO₂-uitstoot en gasverbruik is een lagere waarde doorgaans gunstiger.</div><p class="small">De drie woningverwarmingsindicatoren zijn al in de catalogus bekend, maar nog niet in de huidige lokale selectie geïmporteerd. Daarom worden ze hierboven als niet gesynchroniseerd getoond.</p></section>
</main>
</body>
</html>