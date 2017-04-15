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

        return $this->getLocaleOrNotLocaleRouteUrl($localeName, $name, $parameters, $absolute);
    }

    protected function getLocaleOrNotLocaleRouteUrl($localeName = null, $name = null, $parameters = [], $absolute = true)
    {
        try {
            $url = $this->url->route($localeName, $parameters, $absolute);
        } catch (InvalidArgumentException $e) {
            $url = $this->url->route($name, $parameters, $absolute);
        }

        $url = rtrim($url, '?');

        return $url;
    }

    private function translateParameters($locale, $parameters)
    {
        if (!is_array($parameters)) {
            $parameters = array($parameters);
        }

        $translated_parameters = array();
        foreach ($parameters as $parameter) {
            if (!is_numeric($parameter) && Lang::has('routes.!parameters.' . $parameter, $locale)) {
                $translated_parameters[] = Lang::get('routes.!parameters.' . $parameter, [], $locale);
            } else {
                $translated_parameters[] = $parameter;
            }
        }
        return $translated_parameters;
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
