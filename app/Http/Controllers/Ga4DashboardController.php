<?php

namespace App\Http\Controllers;

use App\Services\Ga4ApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class Ga4DashboardController extends Controller
{
    public function __construct(
        protected Ga4ApiService $ga4,
    ) {
    }

    public function index(): View
    {
        // Usar "ontem" como período padrão (1 dia)
        $days = 1;

        $timeline = $this->ga4->customEventsTimeline($days);
        $summary = $this->ga4->customEventsSummary($days);
        $pages = $this->ga4->pageTitlesSummary($days);
        $engagementOverview = $this->ga4->engagementOverview($days);
        $clientProfiles = $this->ga4->clientIdsFromProfile($days);
        $pageViewCounts = $this->ga4->pageViewCountsByClient($days);

        $clientIds = collect($clientProfiles)->pluck('client_id')->unique()->values();
        $clients = [];

        // Consulta direta ao Metabase, replicando a chamada que já validamos em /debug/metabase-raw
        $apiUrl = rtrim(config('metabase.api_url'), '/');
        $apiKey = config('metabase.api_key');
        $databaseId = (int) config('metabase.database_id');

        if ($clientIds->isNotEmpty() && $apiUrl && $apiKey && $databaseId) {
            foreach ($clientIds as $id) {
                $sql = sprintf('SELECT * FROM clients WHERE id = %d', $id);

                $payload = [
                    'database' => $databaseId,
                    'type' => 'native',
                    'native' => [
                        'query' => $sql,
                    ],
                ];

                try {
                    $response = Http::withHeaders([
                        'x-api-key' => $apiKey,
                        'Content-Type' => 'application/json',
                    ])->post("{$apiUrl}/api/dataset", $payload);

                    $status = $response->status();
                    $data = $response->json();

                    // Alguns ambientes retornam 202 (Accepted) mesmo com dados completos.
                    // Consideramos sucesso para qualquer 2xx.
                    if ($status < 200 || $status >= 300) {
                        continue;
                    }

                    if (!isset($data['data']['rows'][0]) || !isset($data['data']['cols'])) {
                        continue;
                    }

                    $rows = $data['data']['rows'];
                    $cols = $data['data']['cols'];

                    // Descobre índices de ID e Nome
                    $idIndex = 0;
                    $nameIndex = 1;
                    foreach ($cols as $index => $col) {
                        $label = strtolower($col['name'] ?? $col['display_name'] ?? '');
                        if ($label === 'id' || $label === 'client_id') {
                            $idIndex = $index;
                        }
                        if ($label === 'name' || $label === 'client' || $label === 'client_name') {
                            $nameIndex = $index;
                        }
                    }

                    foreach ($rows as $row) {
                        $pageViewCount = (int) ($pageViewCounts->get($id, 0));
                        $clients[] = [
                            'id' => $row[$idIndex] ?? null,
                            'name' => $row[$nameIndex] ?? null,
                            'page_view_events' => $pageViewCount,
                        ];
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }

        $totalEvents = $summary->sum('event_count');
        $totalUsers = $engagementOverview['activeUsers'] ?? 0;
        $topEvent = $summary->first();

        $periodLabel = $days === 1 ? 'Ontem' : "Últimos {$days} dias";

        return view('ga4.dashboard', [
            'periodLabel' => $periodLabel,
            'timeline' => $timeline,
            'summary' => $summary,
            'pages' => $pages,
            'engagementOverview' => $engagementOverview,
            'clients' => $clients,
            'clientProfiles' => $clientProfiles,
            'totalEvents' => $totalEvents,
            'totalUsers' => $totalUsers,
            'topEvent' => $topEvent,
        ]);
    }
}


