<?php

namespace CaribouFute\LocaleRoute\Routing;

use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use CaribouFute\LocaleRoute\Routing\RouteLocalizer;
use CaribouFute\LocaleRoute\Routing\UrlLocalizer;
use Config;
use Illuminate\Routing\Router as IlluminateRouter;

class Router
{
    protected $illuminateRouter;
    protected $routeLocalizer;
    protected $urlLocalizer;

    public function __construct(IlluminateRouter $illuminateRouter, RouteLocalizer $routeLocalizer, UrlLocalizer $urlLocalizer)
    {
        $this->illuminateRouter = $illuminateRouter;
        $this->routeLocalizer = $routeLocalizer;
        $this->urlLocalizer = $urlLocalizer;
    }

    public function get($route, $action, array $urls = [])
    {
        $this->makeRoutes('get', $route, $action, $urls);
    }

    public function post($route, $action, array $urls = [])
    {
        $this->makeRoutes('post', $route, $action, $urls);
    }

    public function put($route, $action, array $urls = [])
    {
        $this->makeRoutes('put', $route, $action, $urls);
    }

    public function patch($route, $action, array $urls = [])
    {
        $this->makeRoutes('patch', $route, $action, $urls);
    }

    public function delete($route, $action, array $urls = [])
    {
        $this->makeRoutes('delete', $route, $action, $urls);
    }

    public function options($route, $action, array $urls = [])
    {
        $this->makeRoutes('options', $route, $action, $urls);
    }

    public function makeRoutes($method, $route, $action, array $urls = [])
    {
        $locales = Config::get('localeroute.locales');

        foreach ($locales as $locale) {
            $this->makeRoute($locale, $method, $route, $action, $urls);
        }
    }

    public function makeRoute($locale, $method, $route, $action, array $urls = [])
    {
        $localeAction = $this->addLocaleRouteToAction($locale, $route, $action);
        $url = $this->urlLocalizer->getRouteUrl($locale, $route, $urls);
        $this->makeLaravelRoute($method, $locale, $url, $localeAction);
    }

    public function addLocaleRouteToAction($locale, $route, $action)
    {
        $localeRoute = $this->routeLocalizer->addLocale($locale, $route);

        if (is_array($action)) {
            $action['as'] = $localeRoute;
        } else {
            $action = ['as' => $localeRoute, 'uses' => $action];
        }

        return $action;
    }

    protected function makeLaravelRoute($method, $locale, $url, $action)
    {
        $middleware = SetSessionLocale::class . ':' . $locale;

        return $this->illuminateRouter
            ->$method($url, $action)
            ->middleware($middleware);
    }
}
