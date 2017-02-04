<?php

namespace CaribouFute\LocaleRoute\Routing;

use CaribouFute\LocaleRoute\Prefix\Route as PrefixRoute;
use CaribouFute\LocaleRoute\Prefix\Url as PrefixUrl;
use CaribouFute\LocaleRoute\Routing\RouteCollection;
use Illuminate\Contracts\Routing\Registrar as IlluminateRouter;
use Illuminate\Routing\Route;

class Router
{
    protected $router;
    protected $url;
    protected $route;

    public function __construct(IlluminateRouter $router, PrefixUrl $url, PrefixRoute $route)
    {
        $this->router = $router;
        $this->url = $url;
        $this->route = $route;
    }

    public function any($uri, $action = [])
    {
        return $this->makeRoute('any', $uri, $action);
    }

    public function get($uri, $action = [])
    {
        return $this->makeRoute('get', $uri, $action);
    }

    public function post($uri, $action = [])
    {
        return $this->makeRoute('post', $uri, $action);
    }

    public function put($uri, $action = [])
    {
        return $this->makeRoute('put', $uri, $action);
    }

    public function patch($uri, $action = [])
    {
        return $this->makeRoute('patch', $uri, $action);
    }

    public function delete($uri, $action = [])
    {
        return $this->makeRoute('delete', $uri, $action);
    }

    public function options($uri, $action = [])
    {
        return $this->makeRoute('options', $uri, $action);
    }

    public function makeRoute($method, $uri, $action = [])
    {
        $route = $this->router->$method($uri, $action);
        $route = $this->addLocale($route, $action);
        $this->refreshRoutes();

        return $route;
    }

    public function addLocale(Route $route, $action = [])
    {
        $locale = $this->getActionLocale($route);

        if (!$locale) {
            return $route;
        }

        $route = $this->switchRouteLocale($locale, $route);
        $route = $this->switchUrlLocale($locale, $route, $action);

        $route->middleware($this->makeSetSessionLocale($locale));

        return $route;
    }

    public function getActionLocale(Route $route)
    {
        $locales = $this->getActionLocales($route);

        return is_array($locales) ? last($locales) : $locales;
    }

    public function getActionLocales(Route $route)
    {
        $action = $route->getAction();

        return is_array($action) && isset($action['locale']) ? $action['locale'] : [];
    }

    public function switchRouteLocale($locale, Route $route)
    {
        $name = $route->getName();
        if (!$name) {
            return $route;
        }

        $name = $this->route->switchLocale($locale, $name);
        $route = $this->setRouteName($name, $route);

        return $route;
    }

    public function setRouteName($name, Route $route)
    {
        $action = $route->getAction();
        $action['as'] = $name;
        $route->setAction($action);

        return $route;
    }

    public function switchUrlLocale($locale, Route $route, array $action = [])
    {
        $uri = $this->url->switchLocale($locale, $route->uri(), $action);
        $route->setUri($uri);

        return $route;
    }

    public function makeSetSessionLocale($locale)
    {
        return 'locale.session:' . $locale;
    }

    public function refreshRoutes()
    {
        $routeCollection = new RouteCollection;
        $routeCollection->hydrate($this->router->getRoutes());
        $routeCollection->refresh();
        $this->router->setRoutes($routeCollection);
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->router, $method], $arguments);
    }
}
