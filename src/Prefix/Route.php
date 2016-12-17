<?php

namespace CaribouFute\LocaleRoute\Prefix;

use App;
use Lang;
use CaribouFute\LocaleRoute\Prefix\Base;
use Illuminate\Routing\Router as IlluminateRouter;
use Illuminate\Routing\UrlGenerator;

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
        $parameters = $this->translateParameters($locale, $parameters);

        $localeRoute = $this->switchLocale($locale, $name);
        $localeUrl = $this->url->route($localeRoute, $parameters, $absolute);
        $localeUrl = rtrim($localeUrl, '?');

        return $localeUrl;
    }

    private function translateParameters($locale, $parameters)
    {
        if (!is_array($parameters)) {
            $parameters = array($parameters);
        }

        $translated_parameters = array();
        foreach ($parameters as $parameter) {
            if (!is_numeric($parameter) && Lang::has('routes.!parameters.'.$parameter, $locale)) {
                $translated_parameters[] = Lang::get('routes.!parameters.'.$parameter, [], $locale);
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
