<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('fetch:data')
            ->twiceDaily(10, 22)
            ->timezone(config('app.timezone'))
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command('api:fetch sales')
            ->cron('5 10,22 * * *')
            ->withoutOverlapping()->runInBackground();

        $schedule->command('api:fetch orders')
            ->cron('7 10,22 * * *')
            ->withoutOverlapping()->runInBackground();

        $schedule->command('api:fetch incomes')
            ->cron('9 10,22 * * *')
            ->withoutOverlapping()->runInBackground();

        $schedule->call(function () {
            $d = now()->timezone(config('app.timezone'))->toDateString();

            Artisan::call('api:fetch stocks',['--from'=>$d,'--to'=>$d]);

        })
            ->cron('11 10,22 * * *')
            ->name('api:fetch:stocks:twice-daily')
            ->withoutOverlapping()->runInBackground();
    }



    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
