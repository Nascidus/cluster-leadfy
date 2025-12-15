<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect()->route('ga4.dashboard');
});

Route::get('/login', [\App\Http\Controllers\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [\App\Http\Controllers\LoginController::class, 'logout'])->name('logout');

Route::middleware('dashboard.auth')->group(function () {
    Route::get('/ga4/dashboard', [\App\Http\Controllers\Ga4DashboardController::class, 'index'])
        ->name('ga4.dashboard');
});

Route::get('/debug/metabase-clients', function (
    \App\Services\Ga4ApiService $ga4,
    \App\Services\MetabaseService $metabase,
) {
    // Usa exatamente os mesmos IDs extraídos do GA4 que o dashboard usa
    $profiles = $ga4->clientIdsFromProfile(28);
    $ids = collect($profiles)->pluck('client_id')->unique()->values()->all();
    $clients = $metabase->fetchClientsByIds($ids);

    return response()->json([
        'ids_enviados' => $ids,
        'clientes_retorno' => $clients,
        'profiles' => $profiles,
    ]);
});

Route::get('/debug/metabase-clients-sample', function () {
    $apiUrl = rtrim(config('metabase.api_url'), '/');
    $apiKey = config('metabase.api_key');
    $databaseId = (int) config('metabase.database_id');

    if (!$apiUrl || !$apiKey || !$databaseId) {
        return response()->json([
            'error' => 'Config Metabase incompleta',
        ], 500);
    }

    $sql = 'SELECT * FROM clients LIMIT 5';

    $payload = [
        'database' => $databaseId,
        'type' => 'native',
        'native' => [
            'query' => $sql,
        ],
    ];

    $response = Http::withHeaders([
        'x-api-key' => $apiKey,
        'Content-Type' => 'application/json',
    ])->post("{$apiUrl}/api/dataset", $payload);

    return response()->json([
        'status' => $response->status(),
        'sql' => $sql,
        'raw' => $response->json(),
    ]);
});

Route::get('/debug/metabase-raw', function (Request $request) {
    $apiUrl = rtrim(config('metabase.api_url'), '/');
    $apiKey = config('metabase.api_key');
    $databaseId = (int) config('metabase.database_id');

    if (!$apiUrl || !$apiKey || !$databaseId) {
        return response()->json([
            'error' => 'Config Metabase incompleta',
        ], 500);
    }

    $id = (int) $request->query('id', 44);
    $sql = sprintf('SELECT * FROM clients WHERE id = %d', $id);

    $payload = [
        'database' => $databaseId,
        'type' => 'native',
        'native' => [
            'query' => $sql,
        ],
    ];

    $response = Http::withHeaders([
        'x-api-key' => $apiKey,
        'Content-Type' => 'application/json',
    ])->post("{$apiUrl}/api/dataset", $payload);

    return response()->json([
        'status' => $response->status(),
        'sql' => $sql,
        'raw' => $response->json(),
    ]);
});



