<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiService;
class AddApiService extends Command
{

    protected $signature = 'add:apiService {name}';


    protected $description = 'Создание api service';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $name = $this->argument('name');

        try {
            $service = ApiService::create(['name' => $name]);
            $this->info("Api service '{$service->name}' успешно создан, айди сервиса = '{$service->id}'");
            return 0;
        }catch (\Exception $e){
            $this->info("Сервис '{$name}' не создан");
            $this->line($e->getMessage());
            return 1;
        }
    }
}
