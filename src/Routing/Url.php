<?php

namespace CaribouFute\LocaleRoute\Routing;

use App;
use Config;
use Illuminate\Routing\Router as IlluminateRouter;
use Illuminate\Routing\UrlGenerator;

class Url
{
    protected $url;
    protected $router;

    public function __construct(UrlGenerator $url, IlluminateRouter $router)
    {
        $this->url = $url;
        $this->router = $router;
    }

    public function addLocale($locale, $uri)
    {
        return Config::get('localeroute.add_locale_to_url') ? $locale . '/' . $uri : $uri;
    }

    public function removeLocale($locale, $uri)
    {
        return Config::get('localeroute.add_locale_to_url') ? str_replace($locale . '/', '', $uri) : $uri;
    }

    public function localeRoute($locale = null, $name = null, $parameters = [], $absolute = true)
    {
        $locale = $locale ?? App::getLocale();
        $name = $name ?? $this->router->currentRouteName();

        $localeRoute = $this->switchRouteLocale($locale, $name);
        $localeUrl = $this->url->route($localeRoute, $parameters, $absolute);

        return $localeUrl;
    }

    protected function switchRouteLocale($locale, $route)
    {
        $unlocaleRoute = $this->removeLocaleFromRouteName($route);
        $localeRoute = $this->addLocaleToRouteName($locale, $unlocaleRoute);

        return $localeRoute;
    }

    protected function removeLocaleFromRouteName($route)
    {
        $localePrefix = $this->getRouteNameLocalePrefix($route);
        $unlocaleRoute = str_replace($localePrefix, '', $route);

        return $unlocaleRoute;
    }

    protected function getRouteNameLocalePrefix(string $route)
    {
        foreach (Config::get('localeroute.locales') as $locale) {
            $localePrefix = $locale . '.';
            if (strpos($route, $localePrefix) === 0) {
                return $localePrefix;
            }
        }

        return '';
    }

    protected function addLocaleToRouteName($locale, $route)
    {
        return $locale . '.' . $route;
    }

}
