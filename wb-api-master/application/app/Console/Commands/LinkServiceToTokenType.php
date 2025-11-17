<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiService;
use Illuminate\Support\Facades\DB;
class LinkServiceToTokenType extends Command
{
    protected $signature = 'link:serviceTokenType {service_id} {tokentype_id}';

    protected $description = 'Связывание сервиса и типа токена';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $serviceId = $this->argument('service_id');
        $tokenTypeId = $this->argument('tokentype_id');

        try {
            $service = ApiService::findOrFail($serviceId);

            $service->supportedTokenTypes()->attach($tokenTypeId);

            $this->info("Api сопряжен {$serviceId} с типом токена id = {$tokenTypeId}.");
            return 0;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->error("API или  тип токена не найдены.");
            return 1;
        } catch (\Exception $e) {
            $this->error("Сопряжение не получилось .");
            $this->line($e->getMessage());
            return 1;
        }
    }
}
