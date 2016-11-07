<?php

namespace CaribouFute\LocaleRoute\Localizers;

use App;
use Config;
use Illuminate\Routing\Router as LaraveRouter;
use Illuminate\Routing\UrlGenerator;

class Route
{
    protected $url;
    protected $router;

    public function __construct(UrlGenerator $url, LaraveRouter $router)
    {
        $this->url = $url;
        $this->router = $router;
    }

    public function localeRoute($locale = null, $name = null, $parameters = [], $absolute = true)
    {
        $locale = $locale ?? App::getLocale();
        $name = $name ?? $this->router->currentRouteName();

        $localeRoute = $this->switchLocale($locale, $name);
        $localeUrl = $this->url->route($localeRoute, $parameters, $absolute);

        return $localeUrl;
    }

    public function switchLocale($locale, $route)
    {
        $unlocaleRoute = $this->removeLocale($route);
        $localeRoute = $this->addLocale($locale, $unlocaleRoute);

        return $localeRoute;
    }

    public function removeLocale($route)
    {
        $localePrefix = $this->getLocalePrefix($route);
        $unlocaleRoute = str_replace($localePrefix, '', $route);

        return $unlocaleRoute;
    }

    public function getLocalePrefix(string $route)
    {
        foreach ($this->locales() as $locale) {
            $localePrefix = $locale . '.';

            if (strpos($route, $localePrefix) === 0) {
                return $localePrefix;
            }
        }

        return '';
    }

    public function locales()
    {
        return Config::get('localeroute.locales');
    }

    public function addLocale($locale, $route)
    {
        return $locale . '.' . $route;
    }

}
