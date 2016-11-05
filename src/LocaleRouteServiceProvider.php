<?php

namespace CaribouFute\LocaleRoute;

use CaribouFute\LocaleRoute\Routing\Router as LocaleRouter;
use CaribouFute\LocaleRoute\Routing\Url as LocaleUrl;
use Illuminate\Support\ServiceProvider;

class LocaleRouteServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->publishes([__DIR__ . '/config/localeroute.php' => config_path('localeroute.php')]);
        $this->mergeConfigFrom(__DIR__ . '/config/localeroute.php', 'localeroute');
    }

    public function register()
    {
        $this->app->bind('locale-route', LocaleRouter::class);
        $this->app->bind('locale-url', LocaleUrl::class);
        config('config/localeroute.php');
    }
}
