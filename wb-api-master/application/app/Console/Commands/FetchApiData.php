<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Income;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchApiData extends Command
{

    protected $signature = 'api:fetch {entity : Тип данных (sales, incomes, stocks, orders)}
                                      {--from= : Дата начала в формате Y-m-d}
                                      {--to= : Дата конца в формате Y-m-d}
                                      {--limit=500 : Количество записей на страницу (max 500)}';

    protected $description = 'Загружает данные из API для всех активных аккаунтов';

    protected array $entitiesConfig = [
        'sales' => [
            'model' => Sale::class,
            'endpoint' => '/api/sales',
            'uniqueBy' => ['account_id', 'sale_id'],
            'update_fields' => ['last_change_date', 'total_price', 'discount_percent', 'warehouse_name', 'is_storno', 'for_pay', 'g_number'],
        ],
        'orders' => [
            'model' => Order::class,
            'endpoint' => '/api/orders',
            'uniqueBy' => ['account_id', 'order_id'],
            'update_fields' => ['last_change_date', 'warehouse_name', 'is_cancel', 'cancel_dt'],
        ],
        'incomes' => [
            'model' => Income::class,
            'endpoint' => '/api/incomes',
            'uniqueBy' => ['account_id', 'income_id'],
            'update_fields' => ['last_change_date', 'total_price', 'quantity', 'warehouse_name', 'income_id'],
        ],
        'stocks' => [
            'model' => Stock::class,
            'endpoint' => '/api/stocks',
            'uniqueBy' => ['account_id', 'date', 'supplier_article', 'warehouse_name'],

            'update_fields' => ['price', 'discount', 'quantity', 'quantity_full', 'in_way_to_client', 'in_way_from_client', 'barcode', 'warehouse_name', 'last_change_date'],
        ],
    ];

    public function handle(): int
    {
        $entity = $this->argument('entity');
        if (!isset($this->entitiesConfig[$entity])) {
            $this->error("Неверный тип сущности '{$entity}'.");
            return 1;
        }

        $currentConfig = $this->entitiesConfig[$entity];
        $modelClass = $currentConfig['model'];

        $accounts = Account::with('apiService')->get();
        if ($accounts->isEmpty()) {
            $this->warn('Нет аккаунтов для обработки');
            return 0;
        }

        foreach ($accounts as $account) {
            $this->line('');
            $this->info("Обработка аккаунта '{$account->name}' (ID {$account->id})");

            $apiKey = $account->token_value;
            $baseUrl = 'http://109.73.206.144:6969';
            $apiUrl = $baseUrl . $currentConfig['endpoint'];

            $this->info("Загрузка данных для '{$entity}'");

            $dateFrom = $this->option('from') ? Carbon::parse($this->option('from')) : now()->subDay();
            $dateTo = $this->option('to') ? Carbon::parse($this->option('to')) : now();

            $this->line("Период с {$dateFrom->format('Y-m-d')} по {$dateTo->format('Y-m-d')}");

            $page = 1;
            $totalSynced = 0;

            while (true) {

                $response = Http::timeout(30)->retry(3, 100)->get($apiUrl, [
                    'dateFrom' => $dateFrom->format('Y-m-d'),
                    'dateTo' => $dateTo->format('Y-m-d'),
                    'page' => $page,
                    'limit' => (int)$this->option('limit'),
                    'key' => $apiKey,
                ]);

                if ($response->failed()) {
                    $this->error("Ошибка API для аккаунта {$account->id} на странице {$page}. Статус " . $response->status());
                    Log::error("API {$entity} ошибка для аккаунта {$account->id}", ['status' => $response->status(), 'body' => $response->body()]);
                    continue 2;
                }

                $data = $response->json('data');

                if (empty($data)) {
                    $this->info('Данных на этой странице больше нету');
                    break;
                }

                $dataWithAccountId = array_map(function ($item) use ($account) {
                    $item['account_id'] = $account->id;
                    $item['created_at'] = now();
                    $item['updated_at'] = now();
                    return $item;
                }, $data);

                $modelClass::upsert(
                    $dataWithAccountId,
                    $currentConfig['uniqueBy'],
                    $currentConfig['update_fields']
                );

                $count = count($data);
                $totalSynced += $count;
                $this->info("Обработано {$count} записей");

                if ($count < (int)$this->option('limit')) {
                    $this->info('Это была последняя страница');
                    break;
                }

                $page++;
            }

            $this->info("Загрузка для '{$entity}' по аккаунту '{$account->name}' завершена всего {$totalSynced}.");
        }


        return 0;
    }
}