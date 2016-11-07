<?php

namespace CaribouFute\LocaleRoute\Routing;

use CaribouFute\LocaleRoute\Locale\Route as LocaleRoute;
use CaribouFute\LocaleRoute\Locale\Url as LocaleUrl;
use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use Closure;
use Config;
use Illuminate\Routing\Router as LaravelRouter;

class Router
{
    protected $laravelRouter;
    protected $localeRoute;
    protected $localeUrl;

    public function __construct(LaravelRouter $laravelRouter, LocaleRoute $localeRoute, LocaleUrl $localeUrl)
    {
        $this->laravelRouter = $laravelRouter;
        $this->localeRoute = $localeRoute;
        $this->localeUrl = $localeUrl;
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
        foreach ($this->locales() as $locale) {
            $this->makeRoute($locale, $method, $route, $action, $urls);
        }
    }

    public function locales()
    {
        return Config::get('localeroute.locales');
    }

    public function makeRoute($locale, $method, $route, $action, array $urls = [])
    {
        $localeAction = $this->addLocaleRouteToAction($locale, $route, $action);
        $url = $this->localeUrl->getRouteUrl($locale, $route, $urls);
        $this->makeLaravelRoute($method, $locale, $url, $localeAction);
    }

    public function addLocaleRouteToAction($locale, $route, $action)
    {
        $action = is_string($action) || is_a($action, Closure::class) ? ['uses' => $action] : $action;
        $action['as'] = $this->localeRoute->addLocale($locale, $route);

        return $action;
    }

    protected function makeLaravelRoute($method, $locale, $url, $action)
    {
        $middleware = $this->makeSetSessionLocale($locale);

        return $this->laravelRouter
            ->$method($url, $action)
            ->middleware($middleware);
    }

    protected function makeSetSessionLocale($locale)
    {
        return SetSessionLocale::class . ':' . $locale;
    }

    public function group(array $attributes, Closure $callback)
    {
        foreach ($this->locales() as $locale) {
            $this->makeLaravelGroup($locale, $attributes, $callback);
        }
    }

    public function makeLaravelGroup($locale, array $attributes, Closure $callback)
    {
        $attributes = $this->addLocaleAs($locale, $attributes);
        $attributes = $this->addLocalePrefix($locale, $attributes);
        $attributes = $this->addSetSessionLocaleMiddleware($locale, $attributes);

        $this->laravelRouter->group($attributes, $callback);
    }

    protected function addLocaleAs($locale, array $attributes)
    {
        $as = $attributes['as'] ?? '';
        $as = $this->localeRoute->addLocale($locale, $as);
        $attributes['as'] = $as;

        return $attributes;
    }

    protected function addLocalePrefix($locale, array $attributes)
    {
        $prefix = $attributes['prefix'] ?? '';
        $prefix = $this->localeUrl->addLocale($locale, $prefix);
        $prefix = rtrim($prefix, '/');
        $attributes['prefix'] = $prefix;

        return $attributes;
    }

    protected function addSetSessionLocaleMiddleware($locale, array $attributes)
    {
        $middlewares = $attributes['middleware'] ?? [];
        $middlewares = is_string($middlewares) ? [$middlewares] : $middlewares;
        $middlewares[] = $this->makeSetSessionLocale($locale);
        $attributes['middleware'] = $middlewares;

        return $attributes;
    }

}
