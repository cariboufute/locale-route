<?php

namespace CaribouFute\LocaleRoute;

use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use CaribouFute\LocaleRoute\Prefix\Route as PrefixRoute;
use CaribouFute\LocaleRoute\Routing\LocaleRouter;
use CaribouFute\LocaleRoute\Routing\Router;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LocaleRouteServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot(Router $router)
    {
        $this->publishes([__DIR__ . '/config/localeroute.php' => config_path('localeroute.php')]);
        $this->mergeConfigFrom(__DIR__ . '/config/localeroute.php', 'localeroute');

        $router->middleware('locale.session', SetSessionLocale::class);
    }

    public function register()
    {
        $this->app->bind('caribou-route', Router::class);
        $this->app->bind('locale-route', LocaleRouter::class);
        $this->app->bind('locale-route-url', PrefixRoute::class);

        config('config/localeroute.php');
    }
}
