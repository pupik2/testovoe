<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
class AddCompany extends Command
{

    protected $signature = 'add:company {name}';

    protected $description = 'Добавление новой компании';

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $name = $this->argument('name');

        try {
            $company = Company::create(['name' => $name]);
            $this->info("Компания '{$company->name}' успешно создана, айди компании = '{$company->id}'");
            return 0;
        }catch (\Exception $e){
            $this->info("Компания '{$name}' не создана");
            $this->line($e->getMessage());
            return 1;
        }
    }
}
