<?php

namespace Laraform;

use Illuminate\Support\ServiceProvider;

class LaraformServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->app->routesAreCached()) {
            require __DIR__ . '/routes.php';
        }

        $this->publishes([
            __DIR__ . '/config/laraform.php' => config_path('laraform.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Laraform\Contracts\Validation\Validator', 'Laraform\Validation\Validator');

        $this->app->bind('Illuminate\Contracts\Validation\Validator', function($app, $args){
            return app()->makeWith('Illuminate\Validation\Validator', [
                'data' => [],
                'rules' => [],
            ]);
        });

        
    }
}
