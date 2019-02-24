<?php

namespace CaribouFute\LocaleRoute\Routing;

use CaribouFute\LocaleRoute\Prefix\Route as PrefixRoute;
use CaribouFute\LocaleRoute\Prefix\Url as PrefixUrl;
use CaribouFute\LocaleRoute\Traits\ConfigParams;
use Closure;
use Illuminate\Routing\Route;

class LocaleRouter
{
    use ConfigParams;

    protected $router;
    protected $prefixRoute;
    protected $prefixUrl;

    public function __construct(Router $router, PrefixRoute $prefixRoute, PrefixUrl $prefixUrl)
    {
        $this->router = $router;
        $this->prefixRoute = $prefixRoute;
        $this->prefixUrl = $prefixUrl;
    }

    public function any($route, $action, $options = []): RouteCollection
    {
        return $this->makeRoutes('any', $route, $action, $options);
    }

    public function get($route, $action, $options = []): RouteCollection
    {
        return $this->makeRoutes('get', $route, $action, $options);
    }

    public function post($route, $action, $options = []): RouteCollection
    {
        return $this->makeRoutes('post', $route, $action, $options);
    }

    public function put($route, $action, $options = []): RouteCollection
    {
        return $this->makeRoutes('put', $route, $action, $options);
    }

    public function patch($route, $action, $options = []): RouteCollection
    {
        return $this->makeRoutes('patch', $route, $action, $options);
    }

    public function delete($route, $action, $options = []): RouteCollection
    {
        return $this->makeRoutes('delete', $route, $action, $options);
    }

    public function options($route, $action, $options = []): RouteCollection
    {
        return $this->makeRoutes('options', $route, $action, $options);
    }

    public function makeRoutes($method, $route, $action, $options = []): RouteCollection
    {
        $options = is_string($options) ? $this->convertOptionUrlsToArray($options) : $options;
        $routeCollection = new RouteCollection();

        foreach ($this->locales($options) as $locale) {
            $routeObject = $this->makeRoute($locale, $method, $route, $action, $options);
            $routeCollection->add($routeObject);
        }

        return $routeCollection;
    }

    protected function convertOptionUrlsToArray($options): array
    {
        $newOptions = [];

        foreach ($this->locales() as $locale) {
            $newOptions[$locale] = $options;
        }

        return $newOptions;
    }

    public function makeRoute($locale, $method, $route, $action, $options = []): Route
    {
        $url = $this->prefixUrl->rawRouteUrl($locale, $route, $options);

        $action = $this->convertToControllerAction($action);
        $action = $this->fillAction($locale, $route, $action, $options);

        $middleware = isset($options['middleware']) ? $options['middleware'] : [];

        $route = $this->router
            ->$method($url, $action)
            ->middleware($middleware);

        return $route;
    }

    protected function convertToControllerAction($action)
    {
        return is_string($action) || is_a($action, Closure::class) ?
            ['uses' => $action] :
            $action;
    }

    protected function fillAction($locale, $route, $action, $options): array
    {
        $action['locale'] = $locale;
        $action['as'] = $route;

        if (isset($options['add_locale_to_url'])) {
            $action['add_locale_to_url'] = $options['add_locale_to_url'];
        }

        return $action;
    }

    public function resource($route, $controller, $options = [])
    {
        $registrar = app()->make(ResourceRegistrar::class);
        $registrar->register($route, $controller, $options);
    }
}
