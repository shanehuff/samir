<?php

namespace Modules\ThreeCommas\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ThreeCommas\Contracts\ThreeCommasClientContract;
use Modules\ThreeCommas\Client\ThreeCommasClient;

class ThreeCommasProvider extends ServiceProvider
{
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
}
