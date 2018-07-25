<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //OHLCV
        //$schedule->command('trader:loadOHLCV 1m')->everyMinute();
        $schedule->command('trader:loadOHLCV 5m')->everyMinute();
        //$schedule->command('trader:loadOHLCV 15m')->everyFiveMinutes();
        $schedule->command('trader:loadOHLCV 30m')->everyFifteenMinutes();
        $schedule->command('trader:loadOHLCV 1h')->hourlyAt(1);
        $schedule->command('trader:loadOHLCV 2h')->hourlyAt(1);
        //$schedule->command('trader:loadOHLCV 4h')->hourly();
        //$schedule->command('trader:loadOHLCV 6h')->hourly();
        //$schedule->command('trader:loadOHLCV 8h')->hourly();
        //$schedule->command('trader:loadOHLCV 12h')->hourly();
        $schedule->command('trader:loadOHLCV 1d')->hourly();



        //
        //$schedule->command('trader:loadOHLCV 2h')->hourlyAt(1);
        //


        $schedule->command('trader:loadmarkets')->daily();

        $schedule->command('trader:process')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
