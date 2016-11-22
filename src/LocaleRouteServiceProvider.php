<?php

namespace CaribouFute\LocaleRoute;

use CaribouFute\LocaleRoute\Locale\Route as LocaleRouteUrl;
use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use CaribouFute\LocaleRoute\Routing\Router as LocaleRouter;
use CaribouFute\LocaleRoute\Routing\SubRouter;
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
        $this->app->bind('locale-route', LocaleRouter::class);
        $this->app->bind('sub-route', SubRouter::class);
        $this->app->bind('locale-route-url', LocaleRouteUrl::class);
        config('config/localeroute.php');
    }
}
