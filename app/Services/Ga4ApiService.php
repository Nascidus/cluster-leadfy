<?php

namespace App\Services;

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\InListFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\FilterExpressionList;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Illuminate\Support\Collection;

class Ga4ApiService
{
    protected BetaAnalyticsDataClient $client;
    protected string $propertyId;

    public function __construct()
    {
        $this->propertyId = (string) config('ga4.property_id');

        $this->client = new BetaAnalyticsDataClient([
            'credentials' => config('ga4.credentials_path'),
        ]);
    }

    protected function propertyName(): string
    {
        return 'properties/' . $this->propertyId;
    }

    /**
     * Gera o DateRange para os relatórios.
     *
     * Regra:
     * - Se $days === 1  => somente ontem (start = yesterday, end = yesterday)
     * - Caso contrário => mesmo comportamento anterior (N dias até hoje)
     */
    protected function makeDateRange(int $days): DateRange
    {
        if ($days === 1) {
            return new DateRange([
                'start_date' => 'yesterday',
                'end_date' => 'yesterday',
            ]);
        }

        return new DateRange([
            'start_date' => sprintf('%ddaysAgo', $days),
            'end_date' => 'today',
        ]);
    }

    /**
     * Filtro geral: somente usuários cujo user property \"profile\" contém \"Cliente\".
     *
     * Usa o API name da dimensão de usuário: customUser:profile.
     */
    protected function userProfileFilter(string $contains = 'Cliente'): FilterExpression
    {
        $filter = (new Filter())
            ->setFieldName('customUser:profile')
            ->setStringFilter(
                (new StringFilter())
                    ->setMatchType(StringFilter\MatchType::CONTAINS)
                    ->setValue($contains)
            );

        return (new FilterExpression())
            ->setFilter($filter);
    }

    /**
     * Retorna timeline diária de eventos (últimos N dias).
     */
    public function customEventsTimeline(int $days = 28): Collection
    {
        $request = (new RunReportRequest())
            ->setProperty($this->propertyName())
            ->setDimensions([
                new Dimension(['name' => 'date']),
            ])
            ->setMetrics([
                new Metric(['name' => 'eventCount']),
            ])
            ->setDateRanges([
                $this->makeDateRange($days),
            ]);

        // Considera apenas eventos de interesse
        $eventFilter = (new Filter())
            ->setFieldName('eventName')
            ->setInListFilter(
                (new InListFilter())
                    ->setValues(['first_visit', 'page_view', 'session_start'])
            );

        // Combina filtro de evento com filtro geral de profile
        $request->setDimensionFilter(
            (new FilterExpression())
                ->setAndGroup(
                    (new FilterExpressionList())
                        ->setExpressions([
                            $this->userProfileFilter(),
                            (new FilterExpression())->setFilter($eventFilter),
                        ])
                )
        );

        $response = $this->client->runReport($request);

        return collect($response->getRows())
            ->map(function ($row) {
                $date = $row->getDimensionValues()[0]->getValue();

                return [
                    'date' => $date,
                    'event_count' => (int) $row->getMetricValues()[0]->getValue(),
                ];
            });
    }

    /**
     * Métricas agregadas de engajamento para o período.
     *
     * Calculamos:
     * - averageEngagementTime            => tempo médio de engajamento por usuário ativo (userEngagementDuration / activeUsers)
     * - engagedSessionsPerUser          => sessões engajadas por usuário ativo (métrica nativa)
     * - averageEngagementTimePerSession => tempo médio de engajamento por sessão (userEngagementDuration / engagedSessions)
     */
    public function engagementOverview(int $days = 28): array
    {
        $request = (new RunReportRequest())
            ->setProperty($this->propertyName())
            ->setMetrics([
                new Metric(['name' => 'userEngagementDuration']),
                new Metric(['name' => 'engagedSessions']),
                new Metric(['name' => 'activeUsers']),
            ])
            ->setDateRanges([
                $this->makeDateRange($days),
            ])
            // Aplica o mesmo filtro geral de profile (apenas clientes)
            ->setDimensionFilter($this->userProfileFilter());

        $response = $this->client->runReport($request);
        $rows = $response->getRows();

        if (count($rows) === 0) {
            return [
                'averageEngagementTime' => 0.0,
                'engagedSessionsPerUser' => 0.0,
                'averageEngagementTimePerSession' => 0.0,
                'activeUsers' => 0,
            ];
        }

        $metrics = $rows[0]->getMetricValues();

        $userEngagementDuration = (float) $metrics[0]->getValue(); // em segundos
        $engagedSessions = (float) $metrics[1]->getValue();
        $activeUsers = (float) $metrics[2]->getValue();

        $averageEngagementTime = $activeUsers > 0
            ? $userEngagementDuration / $activeUsers
            : 0.0;

        $averageEngagementTimePerSession = $engagedSessions > 0
            ? $userEngagementDuration / $engagedSessions
            : 0.0;

        $engagedSessionsPerUser = $activeUsers > 0
            ? $engagedSessions / $activeUsers
            : 0.0;

        return [
            'averageEngagementTime' => $averageEngagementTime,
            'engagedSessionsPerUser' => $engagedSessionsPerUser,
            'averageEngagementTimePerSession' => $averageEngagementTimePerSession,
            'activeUsers' => (int) $activeUsers,
        ];
    }

    /**
     * Retorna ranking de eventos (por nome) nos últimos N dias.
     */
    public function customEventsSummary(int $days = 28, int $limit = 10): Collection
    {
        $request = (new RunReportRequest())
            ->setProperty($this->propertyName())
            ->setDimensions([
                new Dimension(['name' => 'eventName']),
            ])
            ->setMetrics([
                new Metric(['name' => 'eventCount']),
                new Metric(['name' => 'totalUsers']),
            ])
            ->setDateRanges([
                $this->makeDateRange($days),
            ]);

        // Apenas filtro geral de profile (sem restringir o nome do evento)
        $request->setDimensionFilter($this->userProfileFilter());

        $response = $this->client->runReport($request);

        $events = collect($response->getRows())
            ->map(function ($row) {
                $dimensionValues = $row->getDimensionValues();
                $metricValues = $row->getMetricValues();

                return [
                    'event_name' => $dimensionValues[0]->getValue(),
                    'event_count' => (int) $metricValues[0]->getValue(),
                    'total_users' => (int) $metricValues[1]->getValue(),
                ];
            })
            ->sortByDesc('event_count')
            ->values();

        if ($limit > 0) {
            return $events->take($limit);
        }

        return $events;
    }

    /**
     * Retorna ranking de páginas (por título) baseado em eventos nos últimos N dias.
     */
    public function pageTitlesSummary(int $days = 28, int $limit = 5): Collection
    {
        $request = (new RunReportRequest())
            ->setProperty($this->propertyName())
            ->setDimensions([
                new Dimension(['name' => 'pageTitle']),
            ])
            ->setMetrics([
                new Metric(['name' => 'eventCount']),
            ])
            ->setDateRanges([
                $this->makeDateRange($days),
            ]);

        // Filtra apenas títulos de página que contenham \"setActiveTab\"
        $filter = (new Filter())
            ->setFieldName('pageTitle')
            ->setStringFilter(
                (new StringFilter())
                    ->setMatchType(StringFilter\MatchType::CONTAINS)
                    ->setValue('setActiveTab')
            );

        // Combina filtro de página com filtro geral de profile
        $request->setDimensionFilter(
            (new FilterExpression())
                ->setAndGroup(
                    (new FilterExpressionList())
                        ->setExpressions([
                            $this->userProfileFilter(),
                            (new FilterExpression())->setFilter($filter),
                        ])
                )
        );

        $response = $this->client->runReport($request);

        $pages = collect($response->getRows())
            ->map(function ($row) {
                $dimensionValues = $row->getDimensionValues();
                $metricValues = $row->getMetricValues();

                return [
                    'page_title' => $dimensionValues[0]->getValue(),
                    'event_count' => (int) $metricValues[0]->getValue(),
                ];
            })
            ->sortByDesc('event_count')
            ->values();

        if ($limit > 0) {
            return $pages->take($limit);
        }

        return $pages;
    }

    /**
     * Retorna IDs de clientes a partir da dimensão de usuário \"profile\" (C7power:Cliente:{id}).
     */
    public function clientIdsFromProfile(int $days = 28): Collection
    {
        $request = (new RunReportRequest())
            ->setProperty($this->propertyName())
            ->setDimensions([
                new Dimension(['name' => 'customUser:profile']),
            ])
            ->setMetrics([
                // Aqui usamos eventCount para contar eventos associados aos perfis
                new Metric(['name' => 'eventCount']),
            ])
            ->setDateRanges([
                $this->makeDateRange($days),
            ]);

        // Mesmo filtro geral de profile + eventos de interesse
        $eventFilter = (new Filter())
            ->setFieldName('eventName')
            ->setInListFilter(
                (new InListFilter())
                    ->setValues(['first_visit', 'page_view', 'session_start'])
            );

        $request->setDimensionFilter(
            (new FilterExpression())
                ->setAndGroup(
                    (new FilterExpressionList())
                        ->setExpressions([
                            $this->userProfileFilter(),
                            (new FilterExpression())->setFilter($eventFilter),
                        ])
                )
        );

        $response = $this->client->runReport($request);

        return collect($response->getRows())
            ->map(function ($row) {
                $profile = $row->getDimensionValues()[0]->getValue();
                $parts = explode(':', (string) $profile);
                $idPart = end($parts);
                $id = is_numeric($idPart) ? (int) $idPart : null;

                return [
                    'profile' => $profile,
                    'client_id' => $id,
                ];
            })
            ->filter(function ($item) {
                return !is_null($item['client_id']);
            })
            ->values();
    }

    /**
     * Retorna contagem de eventos page_view por cliente (ID extraído do profile).
     */
    public function pageViewCountsByClient(int $days = 28): Collection
    {
        $request = (new RunReportRequest())
            ->setProperty($this->propertyName())
            ->setDimensions([
                new Dimension(['name' => 'customUser:profile']),
            ])
            ->setMetrics([
                new Metric(['name' => 'eventCount']),
            ])
            ->setDateRanges([
                $this->makeDateRange($days),
            ]);

        // Filtro: apenas perfis com "Cliente" e apenas eventos page_view
        $eventFilter = (new Filter())
            ->setFieldName('eventName')
            ->setInListFilter(
                (new InListFilter())
                    ->setValues(['page_view'])
            );

        $request->setDimensionFilter(
            (new FilterExpression())
                ->setAndGroup(
                    (new FilterExpressionList())
                        ->setExpressions([
                            $this->userProfileFilter(),
                            (new FilterExpression())->setFilter($eventFilter),
                        ])
                )
        );

        $response = $this->client->runReport($request);

        // Agrupa por client_id somando eventCount
        return collect($response->getRows())
            ->map(function ($row) {
                $profile = $row->getDimensionValues()[0]->getValue();
                $parts = explode(':', (string) $profile);
                $idPart = end($parts);
                $id = is_numeric($idPart) ? (int) $idPart : null;

                return [
                    'client_id' => $id,
                    'event_count' => (int) $row->getMetricValues()[0]->getValue(),
                ];
            })
            ->filter(function ($item) {
                return !is_null($item['client_id']);
            })
            ->groupBy('client_id')
            ->map(function ($items, $clientId) {
                return collect($items)->sum('event_count');
            });
    }
}


