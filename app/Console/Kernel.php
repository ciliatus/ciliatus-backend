<?php

namespace App\Console;

use App\Ciliatus\Automation\Console\CalculateMaintenanceCommand;
use App\Ciliatus\Common\Console\SeedCommand;
use App\Ciliatus\Common\Console\SensorReadingSeedCommand;
use App\Ciliatus\Common\Console\SetupCommand;
use App\Ciliatus\Core\Console\RefreshMonitorCacheCommand;
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
        CalculateMaintenanceCommand::class,
        SeedCommand::class,
        SensorReadingSeedCommand::class,
        SetupCommand::class,
        RefreshMonitorCacheCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('ciliatus:core.refresh_monitor_cache')->everyMinute();
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
