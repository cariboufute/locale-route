<?php

namespace CaribouFute\LocaleRoute\Routing;

use CaribouFute\LocaleRoute\Prefix\Route as PrefixRoute;
use CaribouFute\LocaleRoute\Prefix\Url as PrefixUrl;
use CaribouFute\LocaleRoute\Routing\Router;
use CaribouFute\LocaleRoute\Traits\ConfigParams;
use CaribouFute\LocaleRoute\Traits\ConvertToControllerAction;

class LocaleRouter
{
    use ConvertToControllerAction;
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

    public function get($route, $action, array $options = [])
    {
        $this->makeRoutes('get', $route, $action, $options);
    }

    public function post($route, $action, array $options = [])
    {
        $this->makeRoutes('post', $route, $action, $options);
    }

    public function put($route, $action, array $options = [])
    {
        $this->makeRoutes('put', $route, $action, $options);
    }

    public function patch($route, $action, array $options = [])
    {
        $this->makeRoutes('patch', $route, $action, $options);
    }

    public function delete($route, $action, array $options = [])
    {
        $this->makeRoutes('delete', $route, $action, $options);
    }

    public function options($route, $action, array $options = [])
    {
        $this->makeRoutes('options', $route, $action, $options);
    }

    public function makeRoutes($method, $route, $action, array $options = [])
    {
        foreach ($this->locales() as $locale) {
            $this->makeRoute($locale, $method, $route, $action, $options);
        }
    }

    public function makeRoute($locale, $method, $route, $action, array $options = [])
    {
        $url = $this->prefixUrl->rawRouteUrl($locale, $route, $options);

        $action = $this->convertToControllerAction($action);

        $action['locale'] = $locale;
        $action['as'] = $route;
        $middleware = isset($options['middleware']) ? $options['middleware'] : [];

        $this->router
            ->$method($url, $action)
            ->middleware($middleware);
    }
}
