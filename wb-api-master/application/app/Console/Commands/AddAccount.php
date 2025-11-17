<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;

class AddAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:account {company_id} {name} {api_service_id} {token_type_id} {token_value}';
    protected $description = 'Добавление нового аккаунта';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        try {
            $account = Account::create([
                'company_id' => $this->argument('company_id'),
                'name' => $this->argument('name'),
                'api_service_id' => $this->argument('api_service_id'),
                'token_type_id' => $this->argument('token_type_id'),
                'token_value' => $this->argument('token_value'),
            ]);
            $this->info("Аккаунт'{$account->name}' добавлен успешно с id =  {$account->id}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Не получилось создать аккаунт");
            $this->line($e->getMessage());
            return 1;
        }
    }
}
