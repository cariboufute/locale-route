<?php

namespace CaribouFute\LocaleRoute;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LocaleRouteServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // use this if your package has views
        $this->loadViewsFrom(realpath(__DIR__ . '/resources/views'), 'LocaleRoute');

        // use this if your package has lang files
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'LocaleRoute');

        // use this if your package has routes
        $this->setupRoutes($this->app->router);

        // use this if your package needs a config file
        $this->publishes([
            __DIR__ . '/config/config.php' => config_path('localeroute.php'),
        ]);

        // use the vendor configuration file as fallback
        $this->mergeConfigFrom(
            __DIR__ . '/config/config.php', 'localeroute'
        );
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        $router->group(['namespace' => 'CaribouFute\LocaleRoute\Http\Controllers'], function ($router) {
            require __DIR__ . '/Http/routes.php';
        });
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLocaleRoute();

        // use this if your package has a config file
        // config([
        //         'config/LocaleRoute.php',
        // ]);
    }

    private function registerLocaleRoute()
    {
        $this->app->bind('LocaleRoute', function ($app) {
            return new LocaleRoute($app);
        });
    }
}
