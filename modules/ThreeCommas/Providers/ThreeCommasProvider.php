<?php

namespace Modules\ThreeCommas\Providers;

use Illuminate\Support\ServiceProvider;

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
            // php artisan vendor:publish --provider="Modules\ThreeCommas\Providers\ThreeCommasProvider" --tag="config"
            $this->publishes([
              __DIR__ . '/../config/commas.php' => config_path('commas.php'),
            ], 'config');
        }
    }
}
