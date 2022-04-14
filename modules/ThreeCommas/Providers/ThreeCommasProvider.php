<?php

namespace Modules\ThreeCommas\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ThreeCommas\Contracts\ThreeCommasClientContract;
use Modules\ThreeCommas\Client\ThreeCommasClient;
use Illuminate\Support\Facades\Route;

class ThreeCommasProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/v1/api.php');
        });
    }

    /**
     * Bootstrap services
     * 
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/commas.php' => config_path('commas.php'),
            ], 'config');
        }

        $this->app->bind(ThreeCommasClientContract::class, function () {
            return (new ThreeCommasClient)->setBaseURI(config('commas.base_uri'));
        });
    }

    /**
     * Get route configurations
     *
     * @return array
     */
    private function routeConfiguration(): array
    {
        return [
            'prefix' => 'api/v1',
            'namespace' => 'Modules\ThreeCommas\Http\Controllers\V1'
        ];
    }
}
