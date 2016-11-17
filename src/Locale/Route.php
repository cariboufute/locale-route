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

    public function localeRoute($locale = null, $name = null, $parameters = [], $absolute = true)
    {
        $locale = $locale ?: App::getLocale();
        $name = $name ?: $this->getCurrentRouteName();

        $localeRoute = $this->switchLocale($locale, $name);
        $localeUrl = $this->url->route($localeRoute, $parameters, $absolute);
        $localeUrl = rtrim($localeUrl, '?');

        return $localeUrl;
    }

    public function getCurrentRouteName()
    {
        $currentRoute = $this->laravelRouter->current();
        $currentRouteName = $currentRoute ? $currentRoute->getName() : '';

        return $currentRouteName;
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

    public function getLocalePrefix($route)
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

    public function otherLocale($locale = null, $parameters = null, $absolute = true)
    {
        $name = $this->getCurrentRouteName();
        $parameters = $parameters ?: $this->getCurrentRouteParameters();

        return $this->localeRoute($locale, $name, $parameters, $absolute);
    }

    public function getCurrentRouteParameters()
    {
        $currentRoute = $this->laravelRouter->current();
        $currentRouteParameters = $currentRoute ? $currentRoute->parameters() : [];

        return $currentRouteParameters;
    }
}
