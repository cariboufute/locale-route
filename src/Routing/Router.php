<?php

namespace CaribouFute\LocaleRoute\Routing;

use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use CaribouFute\LocaleRoute\Routing\Url;
use Config;
use Illuminate\Routing\Router as IlluminateRouter;

class Router
{
    protected $router;
    protected $url;

    public function __construct(IlluminateRouter $router, Url $url)
    {
        $this->router = $router;
        $this->url = $url;
    }

    public function get($route, $action, array $urls = [])
    {
        $this->makeMethodRoutes('get', $route, $action, $urls);
    }

    public function post($route, $action, array $urls = [])
    {
        $this->makeMethodRoutes('post', $route, $action, $urls);
    }

    public function put($route, $action, array $urls = [])
    {
        $this->makeMethodRoutes('put', $route, $action, $urls);
    }

    public function patch($route, $action, array $urls = [])
    {
        $this->makeMethodRoutes('patch', $route, $action, $urls);
    }

    public function delete($route, $action, array $urls = [])
    {
        $this->makeMethodRoutes('delete', $route, $action, $urls);
    }

    public function options($route, $action, array $urls = [])
    {
        $this->makeMethodRoutes('options', $route, $action, $urls);
    }

    public function makeMethodRoutes($method, $route, $action, array $urls = [])
    {
        $locales = Config::get('localeroute.locales');

        foreach ($locales as $locale) {
            $this->makeMethodRoute($locale, $method, $route, $action, $urls);
        }
    }

    public function makeMethodRoute($locale, $method, $route, $action, array $urls = [])
    {
        $localeAction = $this->addLocaleRouteToAction($locale, $route, $action);
        $url = $this->url->getRouteUrl($locale, $route, $urls);
        $this->makeIlluminateRoute($method, $locale, $url, $localeAction);
    }

    public function addLocaleRouteToAction($locale, $route, $action)
    {
        $localeRoute = $this->url->addLocaleToRouteName($locale, $route);

        if (is_array($action)) {
            $action['as'] = $localeRoute;
        } else {
            $action = ['as' => $localeRoute, 'uses' => $action];
        }

        return $action;
    }

    protected function makeIlluminateRoute($method, $locale, $url, $action)
    {
        $middlewareCommand = SetSessionLocale::class . ':' . $locale;

        return $this->router
            ->$method($url, $action)
            ->middleware($middlewareCommand);
    }
}
