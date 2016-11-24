<?php

namespace CaribouFute\LocaleRoute\Routing;

use Illuminate\Contracts\Routing\Registrar as IlluminateRouter;
use Illuminate\Routing\Route;

class CaribouRouter
{
    protected $router;

    public function __construct(IlluminateRouter $router)
    {
        $this->router = $router;
    }

    public function getRouter()
    {
        return $this->router;
    }

    protected function addActionLocale(Route $route)
    {
        $locale = $this->getActionLocale($route);

        if (!$locale) {
            return $route;
        }

        //Switch route name locale
        //Switch route url locale

        return $route;
    }

    public function getActionLocale(Route $route)
    {
        $locales = $this->getActionLocales($route);

        if (!$locales) {
            return null;
        }

        return is_array($locales) ? collect($locales)->last() : $locales;
    }

    public function getActionLocales(Route $route)
    {
        $action = $route->getAction();

        return is_array($action) && isset($action['locale']) ? $action['locale'] : null;
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->router, $method], $arguments);
    }
}
