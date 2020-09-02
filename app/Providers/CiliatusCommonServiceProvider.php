<?php

namespace App\Providers;

use App\Ciliatus\Common\Console\SeedCommand;
use App\Ciliatus\Common\Console\SensorReadingSeedCommand;
use App\Ciliatus\Common\Console\SetupCommand;
use App\Ciliatus\Common\Models\Alert;
use App\Ciliatus\Common\Observers\AlertObserver;
use Illuminate\Support\ServiceProvider;

class CiliatusCommonServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->load();
        $this->schedule();
        $this->observe();
    }

    private function load()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
    }

    private function schedule()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupCommand::class,
                SeedCommand::class,
                SensorReadingSeedCommand::class
            ]);
        }
    }

    private function observe()
    {
        Alert::observe(AlertObserver::class);
    }

}
