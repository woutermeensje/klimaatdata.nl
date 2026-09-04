<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hoe groen is Delft? — Klimaatdata.nl</title>
    <style>
        :root { --ink:#18332b; --muted:#61716b; --line:#d7e2dc; --paper:#f5f7f2; --card:#fff; --green:#176b4d; --mint:#dff1e5; --orange:#d8793e; }
        * { box-sizing:border-box; }
        body { margin:0; color:var(--ink); background:var(--paper); font-family:Georgia, 'Times New Roman', serif; line-height:1.55; }
        .wrap { max-width:1160px; margin:auto; padding:28px 22px 72px; }
        .nav { display:flex; justify-content:space-between; gap:16px; align-items:center; font:600 14px -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }
        a { color:var(--green); } .nav a { text-decoration:none; }
        .eyebrow { color:var(--orange); text-transform:uppercase; letter-spacing:.12em; font:700 12px -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }
        h1 { max-width:780px; margin:34px 0 12px; font-size:clamp(2.4rem,6vw,5.3rem); line-height:.98; letter-spacing:-.03em; font-weight:500; }
        h2 { margin:0 0 12px; font-size:1.45rem; font-weight:500; } h3 { margin:0 0 8px; font-size:1rem; }
        .intro { max-width:680px; color:var(--muted); font-size:1.12rem; } .meta { color:var(--muted); font:13px -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }
        .hero { border-bottom:1px solid var(--line); padding-bottom:42px; }
        .stats,.grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-top:26px; }
        .stat,.panel { background:var(--card); border:1px solid var(--line); padding:20px; }
        .stat { border-top:4px solid var(--green); } .stat strong { display:block; margin:5px 0; font-size:2rem; font-weight:500; }
        .stat span,.label { color:var(--muted); font:12px -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }
        .section { padding-top:46px; } .section-head { display:flex; justify-content:space-between; gap:20px; align-items:end; margin-bottom:14px; }
        .grid.two { grid-template-columns:repeat(2,1fr); }
        .metric { display:flex; justify-content:space-between; gap:15px; padding:11px 0; border-bottom:1px solid var(--line); }
        .metric:last-child { border-bottom:0; } .metric strong { white-space:nowrap; font-weight:600; }
        .note { margin-top:16px; padding:14px 16px; background:var(--mint); font-size:.93rem; }
        table { width:100%; border-collapse:collapse; font:14px -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }
        th,td { padding:10px 8px; border-bottom:1px solid var(--line); text-align:left; } th { color:var(--muted); font-size:11px; text-transform:uppercase; letter-spacing:.06em; }
        td:not(:first-child),th:not(:first-child) { text-align:right; } .highlight { background:var(--mint); font-weight:600; } .missing { color:var(--muted); font-style:italic; }
        .bar-row { display:grid; grid-template-columns:145px 1fr 64px; align-items:center; gap:10px; margin:13px 0; font:13px -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }
        .bar { height:9px; background:#e7eee9; } .bar i { display:block; height:100%; background:var(--green); }
        @media(max-width:720px) { .stats,.grid,.grid.two { grid-template-columns:1fr; } .section-head { display:block; } .bar-row { grid-template-columns:115px 1fr 54px; } h1 { font-size:3.2rem; } }
    </style>
</head>
<body>
<main class="wrap">
    <nav class="nav"><a href="/">Klimaatdata.nl</a><a href="/resultaten">Alle resultaten</a></nav>
    <header class="hero">
        <div class="eyebrow">Delft · lokale klimaatdata</div>
        <h1>Hoe groen en klimaatbestendig is Delft?</h1>
        <p class="intro">Een dataprofiel van Delft op basis van de lokaal opgeslagen Klimaatmonitor-cijfers. De pagina laat zien waar Delft vooruitgaat en waar de dataset nog geen antwoord geeft.</p>
        <p class="meta">Laatste beschikbare meting: {{ $comparisonYear ?? 'onbekend' }} · vergelijking met Zuid-Hollandse steden</p>
        <div class="stats">
            @foreach ([['code'=>'co2_totaal_inw','label'=>'CO₂ per inwoner','suffix'=>'ton/inwoner'],['code'=>'perc_he_combi','label'=>'Hernieuwbare energie','suffix'=>'%'],['code'=>'kern341a_evt_pcteig','label'=>'Elektrische auto’s','suffix'=>'%']] as $stat)
                <div class="stat"><span>{{ $stat['label'] }}</span>
                    @if (isset($delftValues[$stat['code']]))
                        <strong>{{ number_format((float) $delftValues[$stat['code']]['value'], 1, ',', '.') }} {{ $stat['suffix'] }}</strong><span>{{ $delftValues[$stat['code']]['period'] }}</span>
                    @else <strong class="missing">Niet beschikbaar</strong> @endif
                </div>
            @endforeach
        </div>
    </header>

    <section class="section">
        <div class="section-head"><div><div class="eyebrow">01 · Energie en uitstoot</div><h2>De transitie in cijfers</h2></div><span class="meta">Delft versus Zuid-Holland</span></div>
        <div class="grid two">
            <div class="panel">
                @foreach ([['co2_totaal_inw','Totale bekende CO₂-uitstoot per inwoner'],['co2go_inw','CO₂-uitstoot gebouwde omgeving per inwoner'],['co2verv_inw','CO₂-uitstoot verkeer en vervoer per inwoner'],['perc_he_combi','Aandeel hernieuwbare energie'],['perc_he_el_combi','Aandeel hernieuwbare elektriciteit']] as [$code,$label])
                    <div class="metric"><span>{{ $label }}</span><strong>{{ isset($delftValues[$code]) ? number_format((float) $delftValues[$code]['value'], 2, ',', '.') . ' ' . ($indicators[$code]->unit ?? '') : 'Niet beschikbaar' }}</strong></div>
                @endforeach
            </div>
            <div class="panel">
                @foreach ([['kern331b_gas_wont','Gemiddeld aardgasverbruik woningen'],['kern335a_zmw_kldak','Zonnepanelen kleine systemen'],['kern323a_zmw_grdak','Zonnepanelen grote systemen'],['wind_turbines','Aantal windturbines']] as [$code,$label])
                    <div class="metric"><span>{{ $label }}</span><strong>{{ isset($delftValues[$code]) ? number_format((float) $delftValues[$code]['value'], 2, ',', '.') . ' ' . ($indicators[$code]->unit ?? '') : 'Niet beschikbaar' }}</strong></div>
                @endforeach
                <div class="note">Een lagere uitstoot en een lager gasverbruik zijn gunstig. Bij hernieuwbare energie, zonnepanelen en laadpunten geldt meestal: hoger is beter.</div>
                <p class="meta">Elektrische verwarming, stadsverwarming en aardgasverwarming zijn in de huidige lokale selectie nog niet als aparte indicator geïmporteerd.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-head"><div><div class="eyebrow">02 · Mobiliteit</div><h2>Van brandstof naar elektrisch</h2></div></div>
        <div class="grid two">
            <div class="panel">
                @foreach ([['elekvoert_bev','Batterij-elektrische voertuigen'],['elekvoert_phev','Plug-in hybrides'],['elekvoert_fcev','Waterstofauto’s'],['kern341a_evt_pcteig','EV-aandeel personenauto’s'],['ldpnt_totaal','Alle laadpunten'],['kern342a_lpt_pbreg','Publieke reguliere laadpunten'],['kern342b_lpt_pbsnl','Publieke snellaadpunten']] as [$code,$label])
                    <div class="metric"><span>{{ $label }}</span><strong>{{ isset($delftValues[$code]) ? number_format((float) $delftValues[$code]['value'], 1, ',', '.') . ' ' . ($indicators[$code]->unit ?? '') : 'Niet beschikbaar' }}</strong></div>
                @endforeach
            </div>
            <div class="panel"><h3>Autobezit naar brandstof</h3>
                @foreach ([['wp_benz','Benzine'],['wp_die','Diesel'],['wpelek','Elektrisch'],['wpphev','PHEV'],['wpgas','CNG'],['wplpg','LPG'],['wpfcev','Waterstof']] as [$code,$label])
                    @if (isset($delftValues[$code]))
                        <div class="bar-row"><span>{{ $label }}</span><div class="bar"><i style="width:{{ min(100, ((float) $delftValues[$code]['value'] / max(1, (float) ($delftValues['wp_tot']['value'] ?? 1))) * 100) }}%"></i></div><strong>{{ number_format((float) $delftValues[$code]['value'], 0, ',', '.') }}</strong></div>
                    @endif
                @endforeach
                <div class="metric"><span>Auto’s per huishouden</span><strong>{{ isset($delftValues['pauto_hhgem']) ? number_format((float) $delftValues['pauto_hhgem']['value'], 2, ',', '.') : 'Niet beschikbaar' }}</strong></div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-head"><div><div class="eyebrow">03 · Zuid-Holland</div><h2>Hoe verhoudt Delft zich tot andere steden?</h2></div><span class="meta">Laatste waarde per stad</span></div>
        <div class="panel"><table><thead><tr><th>Gemeente</th><th>CO₂/inwoner</th><th>Hernieuwbaar</th><th>EV-aandeel</th></tr></thead><tbody>
            @foreach ($comparison as $city)
                <tr class="{{ $city['name'] === 'Delft' ? 'highlight' : '' }}"><td>{{ $city['name'] }}</td><td>{{ $city['co2'] !== null ? number_format((float) $city['co2'], 2, ',', '.') . ' ton' : '—' }}</td><td>{{ $city['renewable'] !== null ? number_format((float) $city['renewable'], 1, ',', '.') . '%' : '—' }}</td><td>{{ $city['ev_share'] !== null ? number_format((float) $city['ev_share'], 1, ',', '.') . '%' : '—' }}</td></tr>
            @endforeach
        </tbody></table><p class="meta">Dit is een vergelijking van beschikbare waarden, geen samengestelde duurzaamheidsranglijst. Gemeenten verschillen in bebouwing, industrie, infrastructuur en registratie van leaseauto’s.</p></div>
    </section>

    <section class="section"><div class="section-head"><div><div class="eyebrow">04 · De ontbrekende helft</div><h2>Klimaatbestendig is meer dan energie</h2></div></div>
        <div class="panel"><p>De huidige Klimaatmonitor-import bevat geen meetwaarden voor hittestress, wateroverlast, droogte, biodiversiteit of luchtkwaliteit. Deze pagina kan dus wel iets zeggen over de verduurzaming van Delft, maar nog niet over de fysieke weerbaarheid van de stad.</p><p class="meta">Voor een compleet artikel zijn aanvullende bronnen nodig voor bijvoorbeeld gevoelstemperatuur, groen- en boomdekking, waterdiepte bij extreme neerslag, droogterisico, natuurkwaliteit en fijnstof.</p></div>
    </section>
</main>
</body>
</html>