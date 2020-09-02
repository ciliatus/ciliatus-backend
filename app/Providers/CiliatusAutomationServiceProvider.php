<?php

namespace App\Providers;

use App\Ciliatus\Automation\Observers\HabitatObserver;
use App\Ciliatus\Automation\Console\CalculateMaintenanceCommand;
use App\Ciliatus\Core\Models\Habitat;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class CiliatusAutomationServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->load();
        $this->observe();
        $this->providers();
        $this->schedule();
    }

    private function load()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'ciliatus.automation');
    }

    private function observe()
    {
        Habitat::observe(HabitatObserver::class);
    }

    private function providers()
    {
        $this->app->register(CiliatusAutomationEventServiceProvider::class);
    }

    private function schedule()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CalculateMaintenanceCommand::class
            ]);

            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->command(CalculateMaintenanceCommand::class)->dailyAt('06:00');
            });
        }
    }

}
