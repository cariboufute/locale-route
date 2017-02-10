<?php

namespace CaribouFute\LocaleRoute\TestHelpers;

use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use CaribouFute\LocaleRoute\Prefix\Route as PrefixRoute;
use CaribouFute\LocaleRoute\Routing\LocaleRouter;
use Illuminate\Support\Facades\Route;

trait EnvironmentSetUp
{
    protected $locales;
    protected $addLocaleToUrl;

    protected function getEnvironmentSetUp($app)
    {
        $this->locales = ['fr', 'en'];
        $this->addLocaleToUrl = true;

        $app['config']->set('localeroute.locales', $this->locales);
        $app['config']->set('localeroute.add_locale_to_url', $this->addLocaleToUrl);

        $app['locale-route'] = app()->make(LocaleRouter::class);
        $app['locale-route-url'] = app()->make(PrefixRoute::class);

        if (method_exists($app['router'], 'aliasMiddleware')) {
            $app['router']->aliasMiddleware('locale.session', SetSessionLocale::class);
        } else {
            $app['router']->middleware('locale.session', SetSessionLocale::class);
        }
    }

    public function getRouteInfo()
    {
        $routeColl = collect(Route::getRoutes()->getRoutes());
        $routeInfo = $routeColl->map(function ($route) {
            return [
                'methods' => $route->methods(),
                'name' => $route->getName(),
                'uri' => $route->uri(),
            ];
        });

        return $routeInfo;
    }

    public function ddRouteInfo()
    {
        dd($this->getRouteInfo());
    }
}
