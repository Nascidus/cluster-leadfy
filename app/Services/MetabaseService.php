<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetabaseService
{
    public function fetchClientsByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        if (empty($ids)) {
            Log::debug('MetabaseService.fetchClientsByIds - nenhum ID recebido do GA4');
            return [];
        }

        $apiUrl = rtrim(config('metabase.api_url'), '/');
        $apiKey = config('metabase.api_key');
        $databaseId = (int) config('metabase.database_id');

        if (!$apiUrl || !$apiKey || !$databaseId) {
            Log::warning('Metabase config incompleta para fetchClientsByIds');

            return [];
        }

        $clients = [];

        // Para compatibilizar com o comportamento validado via curl (1 ID por vez),
        // consultamos o Metabase individualmente para cada ID.
        foreach ($ids as $id) {
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

                if (!$response->ok()) {
                    Log::warning('Erro ao consultar Metabase para ID específico', [
                        'id' => $id,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    continue;
                }

                $data = $response->json();

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
                    $clients[] = [
                        'id' => $row[$idIndex] ?? null,
                        'name' => $row[$nameIndex] ?? null,
                    ];
                }
            } catch (\Throwable $e) {
                Log::error('Exceção ao consultar Metabase para ID específico', [
                    'id' => $id,
                    'message' => $e->getMessage(),
                ]);
                continue;
            }
        }

        return $clients;
    }
}


