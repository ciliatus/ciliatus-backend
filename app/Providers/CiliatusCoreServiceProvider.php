<?php

namespace App\Providers;

use App\Ciliatus\Core\Console\RefreshMonitorCacheCommand;
use App\Ciliatus\Core\Models\Animal;
use App\Ciliatus\Core\Observers\AnimalObserver;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class CiliatusCoreServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publish();
        $this->load();
        $this->observe();
        $this->schedule();
    }

    private function load()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'ciliatus.core');
    }

    private function schedule()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RefreshMonitorCacheCommand::class
            ]);

            $this->app->booted(function () {
                $schedule = app(Schedule::class);
                $schedule->command('ciliatus:core.refresh_monitor_cache')->everyMinute();
            });
        }
    }

    private function publish()
    {
        $this->publishes([
            __DIR__ . '/config.php' => config_path('ciliatus_core.php')
        ]);
    }

    private function observe()
    {
        Animal::observe(AnimalObserver::class);
    }

}
