<?php

namespace CaribouFute\LocaleRoute\Prefix;

use App;
use CaribouFute\LocaleRoute\Prefix\Base;
use Illuminate\Routing\Router as IlluminateRouter;
use Illuminate\Routing\UrlGenerator;
use InvalidArgumentException;
use Lang;

class Route extends Base
{
    protected $separator = '.';
    protected $url;
    protected $router;

    public function __construct(UrlGenerator $url, IlluminateRouter $router)
    {
        $this->url = $url;
        $this->router = $router;
    }

    public function localeRoute($locale = null, $name = null, $parameters = [], $absolute = true)
    {
        $locale = $locale ?: App::getLocale();
        $name = $name ?: $this->getCurrentRouteName();
        $localeName = $this->switchLocale($locale, $name);
        $parameters = $this->translateParameters($locale, $parameters);

        return $this->getRouteUrl($localeName, $name, $parameters, $absolute);
    }

    protected function getRouteUrl($localeName = null, $name = null, $parameters = [], $absolute = true)
    {
        try {
            $url = $this->url->route($localeName, $parameters, $absolute);
        } catch (InvalidArgumentException $e) {
            $url = $this->url->route($name, $parameters, $absolute);
        }

        $url = rtrim($url, '?');

        return $url;
    }

    protected function translateParameters($locale, $parameters)
    {
        $parameters = is_array($parameters) ? $parameters : [$parameters];
        $localeParams = [];

        foreach ($parameters as $param) {
            if (!is_numeric($param) && Lang::has('routes.!parameters.' . $param, $locale)) {
                $localeParams[] = Lang::get('routes.!parameters.' . $param, [], $locale);
            } else {
                $localeParams[] = $param;
            }
        }
        return $localeParams;
    }

    public function getCurrentRouteName()
    {
        $currentRoute = $this->router->current();
        $currentRouteName = $currentRoute ? $currentRoute->getName() : '';

        return $currentRouteName;
    }

    public function otherLocale($locale = null, $parameters = null, $absolute = true)
    {
        $name = $this->getCurrentRouteName();
        $parameters = $parameters ?: $this->getCurrentRouteParameters();

        return $this->localeRoute($locale, $name, $parameters, $absolute);
    }

    public function getCurrentRouteParameters()
    {
        $currentRoute = $this->router->current();
        $currentRouteParameters = $currentRoute ? $currentRoute->parameters() : [];

        return $currentRouteParameters;
    }

    public function otherRoute($name, $parameters = null, $absolute = true)
    {
        return $this->localeRoute(null, $name, $parameters, $absolute);
    }
}
