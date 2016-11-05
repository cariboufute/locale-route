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

    public function localeRoute($locale = null, $route = null)
    {
        $locale = $locale ?? App::getLocale();
        $route = $route ?? $this->router->currentRoute();

        $localeRoute = $this->switchRouteLocale($locale, $route);
        $localeUrl = $this->url->route($localeRoute);

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
        return preg_replace('#\w{2,}\.(.*)#', '$1', $route);
    }

    protected function addLocaleToRouteName($locale, $route)
    {
        return $locale . '.' . $route;
    }

}
