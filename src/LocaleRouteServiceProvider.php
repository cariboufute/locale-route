<?php

namespace CaribouFute\LocaleRoute;

use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use CaribouFute\LocaleRoute\Prefix\Route as PrefixRoute;
use CaribouFute\LocaleRoute\Routing\LocaleRouter;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LocaleRouteServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot(Router $router)
    {
        $this->publishes([__DIR__ . '/config/localeroute.php' => config_path('localeroute.php')]);
        $this->mergeConfigFrom(__DIR__ . '/config/localeroute.php', 'localeroute');

        $this->aliasMiddleware($router);
    }

    /**
     * Adds locale.session middleware alias to both Laravel 5.4 (aliasMiddleware) and Laravel <5.4 (middleware)
     * @param Router $router the app router
     * @return void
     */
    protected function aliasMiddleware(Router $router)
    {
        if (method_exists($router, 'aliasMiddleware')) {
            $router->aliasMiddleware('locale.session', SetSessionLocale::class);
        } else {
            $router->middleware('locale.session', SetSessionLocale::class);
        }
    }

    public function register()
    {
        $this->app->bind('locale-route', LocaleRouter::class);
        $this->app->bind('locale-route-url', PrefixRoute::class);

        config('config/localeroute.php');
    }
}
