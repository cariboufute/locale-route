<?php

namespace CaribouFute\LocaleRoute\Locale;

use App;
use Config;
use Illuminate\Routing\Router as LaravelRouter;
use Illuminate\Routing\UrlGenerator;

class Route
{
    protected $url;
    protected $laravelRouter;

    public function __construct(UrlGenerator $url, LaravelRouter $laravelRouter)
    {
        $this->url = $url;
        $this->laravelRouter = $laravelRouter;
    }

    public function localeRoute($locale = null, $name = null, $parameters = null, $absolute = true)
    {
        $currentRoute = $this->laravelRouter->current();
        $currentRouteName = $currentRoute ? $currentRoute->getName() : '';
        $currentRouteParameters = $currentRoute ? $currentRoute->parameters() : [];

        $locale = $locale ?? App::getLocale();
        $name = $name ?? $currentRouteName;
        $parameters = isset($parameters) ? $parameters : $currentRouteParameters;

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
