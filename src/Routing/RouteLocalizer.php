<?php

namespace CaribouFute\LocaleRoute\Routing;

use App;
use Config;
use Illuminate\Routing\Router as IlluminateRouter;
use Illuminate\Routing\UrlGenerator;

class RouteLocalizer
{
    protected $url;
    protected $router;

    public function __construct(UrlGenerator $url, IlluminateRouter $router)
    {
        $this->url = $url;
        $this->router = $router;
    }

    public function locales()
    {
        return Config::get('localeroute.locales');
    }

    public function localeRoute($locale = null, $name = null, $parameters = [], $absolute = true)
    {
        $locale = $locale ?? App::getLocale();
        $name = $name ?? $this->router->currentRouteName();

        $localeRoute = $this->switchRouteLocale($locale, $name);
        $localeUrl = $this->url->route($localeRoute, $parameters, $absolute);

        return $localeUrl;
    }

    public function switchRouteLocale($locale, $route)
    {
        $unlocaleRoute = $this->removeLocale($route);
        $localeRoute = $this->addLocale($locale, $unlocaleRoute);

        return $localeRoute;
    }

    public function removeLocale($route)
    {
        $localePrefix = $this->getRouteNameLocalePrefix($route);
        $unlocaleRoute = str_replace($localePrefix, '', $route);

        return $unlocaleRoute;
    }

    protected function getRouteNameLocalePrefix(string $route)
    {
        foreach ($this->locales() as $locale) {
            $localePrefix = $locale . '.';

            if (strpos($route, $localePrefix) === 0) {
                return $localePrefix;
            }
        }

        return '';
    }

    public function addLocale($locale, $route)
    {
        return $locale . '.' . $route;
    }

}
