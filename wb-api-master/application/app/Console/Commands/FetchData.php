<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use App\Models\Account;
use App\Models\Data;
use Illuminate\Http\Client\ConnectionException;
class FetchData extends Command
{
    protected $signature = 'fetch:data';

    protected $description = 'Сбор данных из аккаунтов';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $accounts = Account::with('apiService')->get();

        if ($accounts->isEmpty()) {
            $this->warn('Нет аккаунтов');
            return 0;
        }
        foreach ($accounts as $account) {
            $lastDate = Data::where('account_id', $account->id)->max('date');
            $queryParams = $lastDate ? ['dateFrom' => $lastDate] : [];
            $this->comment('Last data received on: ' . ($lastDate ?? 'never'));

            try{
//сделать логику


            }catch (ConnectionException $e){
                Log::error($e);
            }
        }
    }
}
