<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CiliatusMonitoringServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->load();
    }

    private function load()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'ciliatus.monitoring');
    }

}
