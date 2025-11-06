<?php

namespace App\Console\Commands;

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

    protected $description = 'Загружает данные';

    protected array $entitiesConfig = [
        'sales' => [
            'model' => Sale::class,
            'endpoint' => '/api/sales',
            'update_fields' => ['last_change_date', 'total_price', 'discount_percent', 'warehouse_name', 'is_storno', 'for_pay', 'g_number'],
        ],
        'incomes' => [
            'model' => Income::class,
            'endpoint' => '/api/incomes',
            'update_fields' => ['last_change_date', 'total_price', 'quantity', 'warehouse_name', 'income_id'],
        ],
        'stocks' => [
            'model' => Stock::class,
            'endpoint' => '/api/stocks',
            'update_fields' => ['price', 'discount', 'quantity', 'quantity_full', 'in_way_to_client', 'in_way_from_client', 'barcode', 'warehouse_name', 'last_change_date'],
        ],
        'orders' => [
            'model' => Order::class,
            'endpoint' => '/api/orders',
            'update_fields' => ['last_change_date', 'warehouse_name', 'is_cancel', 'cancel_dt', 'status','srid'],
        ],
    ];

    public function handle(): int
    {
        $entity = $this->argument('entity');
        $limit = (int)$this->option('limit');
        $apiKey = config('services.wb_api.key');
        $baseUrl = config('services.wb_api.url');


        $currentConfig = $this->entitiesConfig[$entity];
        $modelClass = $currentConfig['model'];
        $apiUrl = $baseUrl . $currentConfig['endpoint'];

        $this->info("Загрузку данных для '{$entity}'...");

        $dateFrom = $this->option('from') ? Carbon::parse($this->option('from')) : now()->subDay();
        $dateTo = $this->option('to') ? Carbon::parse($this->option('to')) : now();

        $this->line("Период: с {$dateFrom->format('Y-m-d')} по {$dateTo->format('Y-m-d')}. Лимит на страницу: {$limit}.");

        $page = 1;
        $totalSynced = 0;

        while (true) {
            $this->line("... Запрашиваем страницу {$page}");

            $response = Http::timeout(30)->retry(3, 100)->get($apiUrl, [
                'dateFrom' => $dateFrom->format('Y-m-d'),
                'dateTo' => $dateTo->format('Y-m-d'),
                'page' => $page,
                'limit' => $limit,
                'key' => $apiKey,
            ]);

            if ($response->failed()) {
                $this->error("Ошибка API на странице {$page}. Статус: " . $response->status());
                Log::error("API {$entity}", ['status' => $response->status(), 'body' => $response->body()]);
                return self::FAILURE;
            }

            $data = $response->json('data');

            if (empty($data)) {
                $this->info('Данных больше нет');
                break;
            }
            $modelClass::upsert(
                $data,
                $currentConfig['update_fields']
            );

            $count = count($data);
            $totalSynced += $count;
            $this->info("Обработано {$count} записей.");

            if ($count < $limit) {
                $this->info('Получено меньше записей, чем лимит. Это была последняя страница.');
                break;
            }

            $page++;
        }

        $this->info("Загрузка для '{$entity}' завершена! Всего синхронизировано записей: {$totalSynced}.");
        return self::SUCCESS;
    }
}