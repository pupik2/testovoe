<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TokenType;
class AddTokenType extends Command
{

    protected $signature = 'add:tokenType {name}';


    protected $description = 'Добавление типа токена';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $name = $this->argument('name');
        try {
            $type = TokenType::create(['type_name' => $name]);
            $this->info(" Тип токена '{$type->type_name}' создан с id = {$type->id}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Тип токена не создан
            ");
            $this->line($e->getMessage());
            return 1;
        }

    }
}
