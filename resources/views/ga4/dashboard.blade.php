<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <title>GA4 Dashboard - Eventos Customizados</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;700;900&display=swap" rel="stylesheet">
        <style>
            :root {
                --bg: #FFF9ED;
                --bg-soft: #FDFFF1;
                --card: #ffffff;
                --accent: #DAFF01;
                --accent-soft: rgba(218, 255, 1, 0.18);
                --text: #1b1b1b;
                --muted: #6b7280;
                --danger: #fb7185;
                --radius-lg: 18px;
            }
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }
            body {
                font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "SF Pro Text", "Helvetica Neue", sans-serif;
                background-color: var(--bg);
                color: var(--text);
                min-height: 100vh;
                padding: 24px 20px 32px;
            }
            .shell {
                max-width: 1200px;
                margin: 0 auto;
                display: grid;
                grid-template-rows: auto auto 1fr;
                gap: 24px;
            }
            .grid-z {
                display: grid;
                grid-template-columns: minmax(0, 1.4fr) minmax(0, 1.2fr);
                gap: 24px;
            }
            .card {
                background: var(--card);
                border-radius: var(--radius-lg);
                border: 1px solid rgba(0, 0, 0, 0.04);
                box-shadow:
                    0 18px 45px rgba(0, 0, 0, 0.03),
                    0 0 0 1px rgba(255, 255, 255, 0.9);
                padding: 20px 22px;
            }
            .card-soft {
                background: linear-gradient(135deg, #1b1b1b, #373B40);
                border-color: #1b1b1b;
                color: #FFF9ED;
            }
            .header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 24px;
            }
            .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 4px 10px;
                border-radius: 999px;
                background: #FFF9ED;
                border: 1px solid rgba(27, 27, 27, 0.12);
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.12em;
                color: var(--muted);
            }
            .eyebrow-dot {
                width: 7px;
                height: 7px;
                border-radius: 999px;
                background: radial-gradient(circle at 30% 30%, #ffffff, var(--accent));
                box-shadow: 0 0 0 4px rgba(218, 255, 1, 0.25);
            }
            .title {
                font-size: 24px;
                font-weight: 600;
                letter-spacing: -0.03em;
                margin-top: 10px;
                margin-bottom: 4px;
            }
            .subtitle {
                font-size: 14px;
                color: var(--muted);
                max-width: 520px;
                line-height: 1.45;
            }
            .pill {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 12px;
                border-radius: 999px;
                background: #FFF9ED;
                border: 1px solid rgba(27, 27, 27, 0.16);
                font-size: 12px;
                color: var(--muted);
            }
            .pill-accent {
                border-color: #DAFF01;
                background: linear-gradient(135deg, #DAFF01, #FDFFF1);
            }
            .pill-dot {
                width: 6px;
                height: 6px;
                border-radius: 999px;
                background: #1b1b1b;
            }
            .pill-label {
                color: var(--text);
                font-weight: 500;
            }
            .pill-sub {
                font-size: 11px;
                color: var(--muted);
            }
            .kpis {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 12px;
            }
            .kpi {
                padding: 10px 12px;
                border-radius: 14px;
                background: #FFF9ED;
                border: 1px solid rgba(27, 27, 27, 0.06);
            }
            .kpi-label {
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.12em;
                color: var(--muted);
                margin-bottom: 6px;
            }
            .kpi-value {
                font-size: 22px;
                font-weight: 600;
                letter-spacing: -0.04em;
            }
            .kpi-meta {
                font-size: 11px;
                color: var(--muted);
                margin-top: 4px;
            }
            .kpi-meta strong {
                color: #1b1b1b;
                font-weight: 500;
            }
            .kpi-chip {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 2px 8px;
                border-radius: 999px;
                background: #1b1b1b;
                color: #DAFF01;
                font-size: 11px;
                margin-left: 6px;
            }
            .kpi-chip span {
                color: var(--accent);
            }
            .canvas {
                position: relative;
                height: 220px;
                margin-top: 8px;
                border-radius: 14px;
                background: linear-gradient(180deg, #FFF9ED, #FDFFF1);
                overflow: hidden;
            }
            .canvas-inner {
                position: absolute;
                inset: 12px 14px 14px 40px;
            }
            .y-axis {
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 40px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                font-size: 10px;
                color: var(--muted);
            }
            .y-axis span {
                opacity: 0.7;
            }
            .grid-lines {
                position: absolute;
                inset: 0 0 16px 40px;
            }
            .grid-lines::before,
            .grid-lines::after {
                content: "";
                position: absolute;
                inset: 0;
                border-radius: 12px;
                background-image: linear-gradient(to right, rgba(27, 27, 27, 0.15) 1px, transparent 1px);
                background-size: 48px 100%;
                opacity: 0.3;
            }
            .grid-lines::after {
                background-image: linear-gradient(to top, rgba(27, 27, 27, 0.18) 1px, transparent 1px);
                background-size: 100% 36px;
                opacity: 0.45;
            }
            .spark-path {
                position: absolute;
                inset: 0 0 16px 40px;
                display: flex;
                align-items: flex-end;
                padding: 8px 10px 10px 10px;
                gap: 6px;
            }
            .spark-bar {
                flex: 1;
                border-radius: 999px;
                background: linear-gradient(to top, rgba(27, 27, 27, 0.15), #1b1b1b);
                box-shadow:
                    0 0 0 1px rgba(27, 27, 27, 0.18),
                    0 10px 30px rgba(0, 0, 0, 0.14);
            }
            .spark-bar.is-zero {
                background: linear-gradient(to top, rgba(27, 27, 27, 0.08), rgba(27, 27, 27, 0.18));
                box-shadow: 0 0 0 1px rgba(27, 27, 27, 0.18);
            }
            .x-axis {
                position: absolute;
                left: 40px;
                right: 0;
                bottom: 0;
                display: flex;
                justify-content: space-between;
                font-size: 10px;
                color: var(--muted);
                padding: 0 10px 2px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 8px;
                font-size: 13px;
            }
            th, td {
                padding: 8px 10px;
                text-align: left;
                white-space: nowrap;
            }
            th {
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.12em;
                color: var(--muted);
                border-bottom: 1px solid rgba(27, 27, 27, 0.08);
            }
            td {
                border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            }
            tr:last-child td {
                border-bottom: none;
            }
            .event-pill {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 3px 10px;
                border-radius: 999px;
                background: #1b1b1b;
                border: 1px solid #DAFF01;
            }
            .event-pill span {
                font-size: 12px;
                color: #FFF9ED;
            }
            .muted {
                color: var(--muted);
            }
            .trend-up {
                color: #4ade80;
                font-size: 11px;
            }
            .trend-down {
                color: var(--danger);
                font-size: 11px;
            }
            .badge {
                display: inline-flex;
                padding: 2px 8px;
                border-radius: 999px;
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.12em;
                border: 1px solid rgba(27, 27, 27, 0.2);
                color: #1b1b1b;
                background-color: #FDFFF1;
            }
            .badge-accent {
                border-color: #1b1b1b;
                color: #DAFF01;
                background: #1b1b1b;
            }
            .footer-note {
                margin-top: 10px;
                font-size: 11px;
                color: var(--muted);
            }
            .footer-note strong {
                color: var(--accent);
                font-weight: 500;
            }
            .streak-dot {
                width: 6px;
                height: 6px;
                border-radius: 999px;
                margin-right: 4px;
                background: radial-gradient(circle at 30% 30%, #bbf7d0, #22c55e);
                box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.25);
            }
            .top-z-row {
                display: grid;
                grid-template-columns: minmax(0, 1.4fr) minmax(0, 1.2fr);
                gap: 24px;
            }
            .bottom-z-row {
                display: grid;
                grid-template-columns: minmax(0, 1.3fr) minmax(0, 1.3fr);
                gap: 24px;
            }
            .pages-chart {
                display: grid;
                gap: 10px;
                margin-top: 8px;
            }
            .pages-row {
                display: grid;
                grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr);
                gap: 10px;
                align-items: center;
            }
            .pages-label {
                font-size: 13px;
                color: var(--text);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .pages-bar-track {
                background: rgba(27, 27, 27, 0.06);
                border-radius: 999px;
                height: 18px;
                overflow: hidden;
            }
            .pages-bar-fill {
                height: 100%;
                border-radius: 999px;
                background: linear-gradient(90deg, #1b1b1b, #DAFF01);
            }
            .pages-meta {
                font-size: 11px;
                color: var(--muted);
                margin-top: 4px;
            }
            .clients-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 8px;
                font-size: 13px;
            }
            .clients-table th,
            .clients-table td {
                padding: 8px 10px;
                text-align: left;
                border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            }
            .clients-table th {
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.12em;
                color: var(--muted);
            }
            /* Ajustes de contraste dentro do card escuro (KPIs principais) */
            .card-soft .kpi {
                background: rgba(255, 249, 237, 0.06);
                border-color: rgba(255, 249, 237, 0.18);
            }
            .card-soft .kpi-label,
            .card-soft .kpi-value,
            .card-soft .kpi-meta {
                color: #FFF9ED;
            }
            .card-soft .kpi-meta strong {
                color: #DAFF01;
            }
            .event-legend {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                margin-top: 6px;
                font-size: 11px;
            }
            .event-legend-pill {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 3px 10px;
                border-radius: 999px;
                background: #FFF9ED;
                border: 1px solid rgba(27, 27, 27, 0.14);
                color: #1b1b1b;
            }
            .event-legend-dot {
                width: 6px;
                height: 6px;
                border-radius: 999px;
                background: #1b1b1b;
            }
            @media (max-width: 960px) {
                body {
                    padding: 20px 14px 28px;
                }
                .shell {
                    gap: 20px;
                }
                .grid-z,
                .top-z-row,
                .bottom-z-row {
                    grid-template-columns: minmax(0, 1fr);
                }
                .header {
                    flex-direction: column;
                }
                .kpis {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }
            @media (max-width: 640px) {
                .kpis {
                    grid-template-columns: minmax(0, 1fr);
                }
                .title {
                    font-size: 20px;
                }
            }
        </style>
    </head>
    <body>
        <div class="shell">
            <div class="top-z-row">
                <div class="card">
                    <div class="header">
                        <div>
                            <div class="eyebrow">
                                <div class="eyebrow-dot"></div>
                                <span>GA4 · Eventos Customizados</span>
                            </div>
                            <h1 class="title">Visão de Engajamento GA4</h1>
                            <p class="subtitle">
                                Painel focado em <strong>eventos customizados</strong> da propriedade GA4,
                                com visão consolidada de volume, usuários e distribuição diária no período selecionado.
                            </p>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                            <div class="pill pill-accent">
                                <span class="pill-dot"></span>
                                <div>
                                    <div class="pill-label">{{ $periodLabel }}</div>
                                    <div class="pill-sub">Property ID · {{ config('ga4.property_id') }}</div>
                                </div>
                            </div>
                            <div class="pill">
                                <span class="pill-sub">Fonte</span>
                                <span class="pill-label">Google Analytics 4 · Data API</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-soft">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <div class="badge badge-accent">KPIs principais</div>
                        <span class="muted" style="font-size: 11px;">Visão geral do período</span>
                    </div>
                    @php
                        $summaryByName = $summary->keyBy('event_name');
                        $firstVisitEvents = (int) optional($summaryByName->get('first_visit'))['event_count'] ?? 0;
                        $sessionStartEvents = (int) optional($summaryByName->get('session_start'))['event_count'] ?? 0;
                        $totalClientes = collect($clients)->pluck('id')->filter()->unique()->count();
                    @endphp
                    <div class="kpis">
                        <div class="kpi">
                            <div class="kpi-label">first_visit</div>
                            <div class="kpi-value">
                                {{ number_format($firstVisitEvents) }}
                            </div>
                            <div class="kpi-meta">
                                Eventos de primeira visita no período.
                            </div>
                        </div>
                        <div class="kpi">
                            <div class="kpi-label">Clientes que acessaram</div>
                            <div class="kpi-value">
                                {{ number_format($totalClientes) }}
                            </div>
                            <div class="kpi-meta">
                                Clientes distintos com atividade no período.
                            </div>
                        </div>
                        <div class="kpi">
                            <div class="kpi-label">session_start (login)</div>
                            <div class="kpi-value">
                                {{ number_format($sessionStartEvents) }}
                            </div>
                            <div class="kpi-meta">
                                Eventos de início de sessão (login) no período.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid-z">
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <div class="badge">Tendência diária</div>
                        <span class="muted" style="font-size: 11px;">Distribuição de eventos por dia</span>
                    </div>
                    <div class="event-legend">
                        <div class="event-legend-pill">
                            <span class="event-legend-dot"></span>
                            <span>first_visit</span>
                        </div>
                        <div class="event-legend-pill">
                            <span class="event-legend-dot"></span>
                            <span>page_view</span>
                        </div>
                        <div class="event-legend-pill">
                            <span class="event-legend-dot"></span>
                            <span>session_start (login)</span>
                        </div>
                    </div>
                    <div class="canvas">
                        <div class="canvas-inner">
                            @php
                                $maxEvents = max(1, $timeline->max('event_count') ?? 1);
                                $points = $timeline->take(28);
                                $labels = $points->filter(function ($row, $index) use ($points) {
                                    return in_array($index, [0, floor($points->count() / 2), $points->count() - 1]);
                                });
                            @endphp
                            <div class="y-axis">
                                <span>{{ number_format($maxEvents) }}</span>
                                <span>{{ number_format((int) round($maxEvents / 2)) }}</span>
                                <span>0</span>
                            </div>
                            <div class="grid-lines"></div>
                            <div class="spark-path">
                                @foreach($points as $row)
                                    @php
                                        $ratio = $maxEvents > 0 ? $row['event_count'] / $maxEvents : 0;
                                        $height = max(6, $ratio * 100);
                                    @endphp
                                    <div class="spark-bar {{ $row['event_count'] === 0 ? 'is-zero' : '' }}" style="height: {{ $height }}%;"></div>
                                @endforeach
                            </div>
                            <div class="x-axis">
                                @foreach($labels as $row)
                                    @php
                                        $dateFormatted = \Carbon\Carbon::createFromFormat('Ymd', $row['date'])->format('d M');
                                    @endphp
                                    <span>{{ $dateFormatted }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <p class="footer-note">
                        <span class="streak-dot"></span>
                        <strong>Tendência:</strong> use a curva diária para identificar picos, quedas e períodos
                        em que campanhas ou ações geraram mais engajamento.
                    </p>
                </div>

                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <div class="badge">Resumo</div>
                        <span class="muted" style="font-size: 11px;">Como ler este painel</span>
                    </div>
                    <p class="subtitle" style="max-width: none; margin-bottom: 10px;">
                        Comece pelos KPIs de topo para entender o tamanho do volume e a base de usuários.
                        Em seguida, use o gráfico diário para enxergar a evolução ao longo do tempo.
                    </p>
                    <p class="subtitle" style="max-width: none;">
                        Use este painel para entender se seus eventos customizados estão sendo disparados
                        como esperado, identificar outliers e priorizar otimizações na jornada de lead.
                    </p>
                </div>
            </div>

            <div class="bottom-z-row">
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <div class="badge">Detalhamento de eventos</div>
                        <span class="muted" style="font-size: 11px;">Ranking de eventos customizados</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Evento</th>
                                <th>Eventos</th>
                                <th>Usuários</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summary as $row)
                                <tr>
                                    <td>
                                        <div class="event-pill">
                                            <span style="width: 6px; height: 6px; border-radius: 999px; background: var(--accent);"></span>
                                            <span>{{ $row['event_name'] }}</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format($row['event_count']) }}</td>
                                    <td>{{ number_format($row['total_users']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="muted">
                                        Nenhum evento encontrado para o período selecionado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <p class="footer-note">
                        Ordenado por <strong>eventCount</strong>. Combine este ranking com a timeline
                        para entender qualidade x volume de engajamento.
                    </p>
                </div>

                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <div class="badge">Páginas (título)</div>
                        <span class="muted" style="font-size: 11px;">Top 5 páginas por eventos</span>
                    </div>
                    @php
                        $maxPageEvents = max(1, $pages->max('event_count') ?? 1);
                    @endphp
                    <div class="pages-chart">
                        @forelse($pages as $page)
                            @php
                                $ratio = $maxPageEvents > 0 ? $page['event_count'] / $maxPageEvents : 0;
                                $width = max(6, $ratio * 100);
                            @endphp
                            <div>
                                <div class="pages-row">
                                    <div class="pages-label" title="{{ $page['page_title'] }}">
                                        {{ $page['page_title'] ?: 'Sem título' }}
                                    </div>
                                    <div style="text-align: right; font-size: 12px;" class="muted">
                                        {{ number_format($page['event_count']) }} eventos
                                    </div>
                                </div>
                                <div class="pages-bar-track">
                                    <div class="pages-bar-fill" style="width: {{ $width }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <p class="muted" style="font-size: 13px;">
                                Nenhuma página com eventos registrada para o período selecionado.
                            </p>
                        @endforelse
                    </div>
                    <p class="pages-meta">
                        Distribuição concentrada em poucas páginas indica pontos-chave da jornada do usuário.
                    </p>
                </div>
            </div>
            <div class="card" style="margin-top: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div class="badge">Clientes que acessaram</div>
                    <span class="muted" style="font-size: 11px;">Derivado da dimensão de usuário profile (C7power:Cliente:ID)</span>
                </div>
                <table class="clients-table">
                    <thead>
                        <tr>
                            <th>ID Cliente</th>
                            <th>Nome</th>
                            <th>Logins (session_start)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td>{{ $client['id'] }}</td>
                                <td>{{ $client['name'] }}</td>
                                <td>{{ number_format($client['session_start_events'] ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="muted">
                                    Nenhum cliente identificado para o período selecionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>


