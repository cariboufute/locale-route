<?php

namespace CaribouFute\LocaleRoute\Routing;

use CaribouFute\LocaleRoute\Locale\Route as LocaleRoute;
use CaribouFute\LocaleRoute\Locale\Url as LocaleUrl;
use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use Config;
use Illuminate\Routing\Router as LaravelRouter;

class Router
{
    protected $laravelRouter;
    protected $routeLocalizer;
    protected $urlLocalizer;

    public function __construct(LaravelRouter $laravelRouter, LocaleRoute $routeLocalizer, LocaleUrl $urlLocalizer)
    {
        $this->laravelRouter = $laravelRouter;
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

        return $this->laravelRouter
            ->$method($url, $action)
            ->middleware($middleware);
    }
}
