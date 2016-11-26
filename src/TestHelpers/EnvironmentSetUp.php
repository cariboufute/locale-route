<?php

namespace CaribouFute\LocaleRoute\TestHelpers;

use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use CaribouFute\LocaleRoute\Prefix\Route as PrefixRoute;
use CaribouFute\LocaleRoute\Routing\LocaleRouter;
use Illuminate\Support\Facades\Route;

trait EnvironmentSetUp
{
    protected $locales;
    protected $addLocaleOption;

    protected function getEnvironmentSetUp($app)
    {
        $this->locales = ['fr', 'en'];
        $this->addLocaleOption = true;

        $app['config']->set('localeroute.locales', $this->locales);
        $app['config']->set('localeroute.add_locale_to_url', $this->addLocaleOption);

        $app['locale-route'] = app()->make(LocaleRouter::class);
        $app['locale-route-url'] = app()->make(PrefixRoute::class);

        $app['router']->middleware('locale.session', SetSessionLocale::class);
    }

    public function ddRoutes()
    {
        $routeColl = collect(Route::getRoutes()->getRoutes());
        dd($routeColl->map(function ($route) {
            return ['name' => $route->getName(), 'uri' => $route->uri()];
        }));
    }
}
