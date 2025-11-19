<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Data;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use carbon\carbon;
use Throwable;

class FetchData extends Command
{
    protected $signature = 'fetch:data';
    protected $description = 'Собирает все необходимые данные из всех API для всех аккаунтов';

    public function handle(): int
    {
        $this->info('Запуск процесса сбора данных...');
        Log::info('Запуск команды fetch:data.');

        $apiMap = [
            'Google Ads' => [
                'base_url' => 'https://teeeest.free.beeceptor.com',
                'entities' => ['campaign_statss'],
            ],
        ];

        $accounts = Account::with('apiService')->get();

        if ($accounts->isEmpty()) {
            $this->warn('Активные аккаунты не найдены');
            return 0;
        }

        foreach ($accounts as $account) {
            $apiName = $account->apiService->name;
            $this->info("Обработка аккаунта '{$account->name}' для API '{$apiName}'");

            if (!isset($apiMap[$apiName])) {
                $this->warn("Для API '{$apiName}' нет инструкций");
                continue;
            }

            $apiConfig = $apiMap[$apiName];
            $baseUrl = $apiConfig['base_url'];

            foreach ($apiConfig['entities'] as $entity) {

                $apiUrl = rtrim($baseUrl, '/') . '/' . $entity;
//dd($entity);
                try {
//                    dd($apiUrl);
                    $lastDate = Data::where('account_id', $account->id)->max('date');
//                    dd($lastDate);
                    $fromDateStr = $lastDate
                        ? Carbon::parse($lastDate)->toDateString()
                        : null;
                    $queryParams = $lastDate ? ['dateFrom' => $lastDate] : [];
//                    dd($queryParams);
                    $max = 5;
                    $base = 500;
                    $cap = 60000;
                    $response = null;

                    for ($i = 1; $i <= $max; $i++) {
                        try {
                            $response = Http::withToken($account->token_value)
                                ->timeout(30)
                                ->acceptJson()
                                ->get($apiUrl, $queryParams);
//                            dd($response->body());

                            if ($response->successful()) {
                                break;
                            }

                            //429
                            if ($response->status() === 429) {
                                $retryAfter = (int)($response->header('Retry-After') ?? 0);
                                $sleepMs = $retryAfter > 0
                                    ? $retryAfter * 1000
                                    : min($cap, $base * (2 ** ($i - 1))) + random_int(0, 250);

                                $this->warn("429 Too Many Requests. Попытка {$i}/{$max}, пауза {$sleepMs} мс");
                                usleep($sleepMs * 1000);
                                continue;
                            }

                            if ($response->serverError()) {
                                $sleepMs = min($cap, $base * (2 ** ($i - 1))) + random_int(0, 250);
                                $this->warn("5xx ({$response->status()}). Попытка {$i}/{$max}, пауза {$sleepMs} мс");
                                usleep($sleepMs * 1000);
                                continue;
                            }

                            break;

                        } catch (ConnectionException $e) {
                            $sleepMs = min($cap, $base * (2 ** ($i - 1))) + random_int(0, 250);
                            $this->warn("Сетевая ошибка: {$e->getMessage()}. Попытка {$i}/{$max}, пауза {$sleepMs} мс");
                            usleep($sleepMs * 1000);
                            continue;
                        }
                    }

                    if (!$response || !$response->ok()) {
                        $this->error("Запрос провалился: status={$response->status()}");
                        Log::error('fetch:data failed', [
                            'account_id' => $account->id,
                            'entity' => $entity,
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);
                        continue;
                    }

//dd($response->json('data'));
//dd($response->json());
                    $contentType = strtolower($response->header('Content-Type', ''));
                    if (!str_contains($contentType, 'json')) {
                        $this->error("Неверный Content-Type: {$contentType} (ожидается application/json)");
                        $this->comment('Body preview: ' . substr((string)$response->body(), 0, 200));
                        continue;
                    }

                    $payload = $response->json();
                    $items = [];

                    if (is_array($payload)) {
                        if (isset($payload['data']) && is_array($payload['data'])) {
                            $items = $payload['data'];
                        } elseif (isset($payload['items']) && is_array($payload['items'])) {
                            $items = $payload['items'];
                        } elseif (function_exists('array_is_list') ? array_is_list($payload) : $this->isListArray($payload)) {
                            $items = $payload;
                        } else {
                            $this->warn("Неожиданная структура ответа");
                        }
                    } else {
                        $this->warn('Ответ не JSON или пустой');
                    }

                    if ($fromDateStr) {
                        $fromDate = Carbon::parse($fromDateStr)->startOfDay();
                        $before = count($items);
                        $items = array_values(array_filter($items, function ($row) use ($fromDate) {
                            if (empty($row['date'])) return false;
                            try {
                                $d = Carbon::parse($row['date'])->startOfDay();
                            } catch (\Throwable $e) {
                                return false;
                            }
                            return $d->greaterThanOrEqualTo($fromDate);
                        }));
                        $this->line("Фильтр по дате >= {$fromDate->toDateString()}: {$before} → " . count($items));
                    }

                    if (empty($items)) {
                        $this->line("{$entity}: новых данных нет.");
                        continue;
                    }


                    $count = 0;
                    foreach ($items as $item) {
                        $extId = $item['id'] ?? $item['external_id'] ?? null;
                        $metricName = $item['metric_name'] ?? null;
                        $metricValue = $item['metric_value'] ?? null;
                        $itemDateStr = $item['date'] ?? null;

                        if (!$extId || !$metricName || !$itemDateStr) {
                            Log::warning('fetch:data skip — не хватает полей', [
                                'account_id' => $account->id,
                                'entity' => $entity,
                                'item' => $item,
                            ]);
                            continue;
                        }
                        $itemDate = Carbon::parse($itemDateStr)->startOfDay();
                        $itemDateSql = $itemDate->toDateString();


                        $latestForKey = Data::where('account_id', $account->id)
                            ->where('external_id', (string)$extId)
                            ->where('metric_name', (string)$metricName)
                            ->max('date'); // может быть null

                        if ($latestForKey && Carbon::parse($latestForKey)->startOfDay()->greaterThan($itemDate)) {

                            $this->line("Skip outdated: {$itemDateSql} < {$latestForKey} (ext_id={$extId}, metric={$metricName})");
                            continue;
                        }


                        Data::updateOrCreate(
                            [
                                'account_id' => $account->id,
                                'external_id' => (string)$extId,
                                'date' => $itemDateSql,
                                'metric_name' => (string)$metricName,
                            ],
                            [
                                'metric_value' => $metricValue,
                            ]
                        );
                        $count++;
                    }

                    $this->line("{$entity}: обработано {$count} записей");

                } catch (Throwable $e) {
                    $this->error("Ошибка при сборе '{$entity}': {$e->getMessage()}");
                    Log::error("Ошибка сбора '{$entity}' для аккаунта {$account->id}: " . $e->getMessage(), [
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }
        }

        $this->info('Процесс сбора данных завершен.');
        return self::SUCCESS;
    }

    protected function isListArray(array $arr): bool
    {
        if ($arr === []) return true;
        $i = 0;
        foreach ($arr as $k => $_) {
            if ($k !== $i++) return false;
        }
        return true;
    }
}