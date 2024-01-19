<?php

namespace LaravelCake\Lead;

use Illuminate\Support\ServiceProvider;

class CakeLeadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Include routes / API
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Register Controller
        $this->app->make('LaravelCake\Lead\Http\Controllers\LeadGenerateContoller');

        // Publish files
        $this->publishes([
            //file source => file destination below
            __DIR__.'/database/migrations/' => database_path('migrations'),
            __DIR__.'/resources/views/cakelead' => resource_path('views/cakelead'),
            __DIR__.'/config' => config_path(),
            __DIR__.'/Models' => app_path('Models')
          ],
          ['migrations']
        );
    }
}
