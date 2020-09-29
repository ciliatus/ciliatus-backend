<?php

namespace App\Providers;

use App\Ciliatus\Common\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class CiliatusServiceProvider extends ServiceProvider
{

    /**
     * @var array
     */
    protected static array $packages = ['Api', 'Automation', 'Common', 'Core', 'Monitoring', 'Web'];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->migrations();
        $this->routes();
        $this->authorize();
    }

    /**
     *
     */
    public function routes()
    {
        foreach (static::$packages as $package) {
            $this->loadRoutesFrom(__DIR__ . '/../Ciliatus/' . $package . '/Http/routes.php');

        }
    }

    /**
     *
     */
    public function authorize()
    {
        $types = ['read', 'write', 'admin'];

        foreach (static::$packages as $package) {
            foreach ($types as $type) {
                Gate::define($type . '-' . strtolower($package), function (User $user) use ($package, $type) {
                    return $user->hasPermission($package, $type);
                });
            }
        }
    }

    public function migrations()
    {
        foreach (static::$packages as $package) {
            $path = __DIR__ . '/../Ciliatus/' . $package . '/Database/migrations';
            if (file_exists($path))
            $this->loadMigrationsFrom($path);
        }
    }
}
