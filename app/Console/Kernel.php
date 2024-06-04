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
        $schedule->command('subscribe:check')->daily()
            ->timezone(config('app.timezone'))
            ->at('00:00')
            ->runInBackground()
            ->evenInMaintenanceMode();

        $schedule->command('sitemap:generate')->weekly();

        //        $increment = 2000;
        //        for($i = 0; $i <= 30000; $i += $increment) {
        //            $schedule->command('sitemap:products', [
        //                'start' => $i,
        //                'increment' => $increment
        //            ])->weekly();
        //        }

        $schedule->command('backup:run')->daily()->at('01:00');
    }

    protected function scheduleTimezone()
    {
        return config('app.timezone');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
