<?php

namespace CaribouFute\LocaleRoute\Routing;

use CaribouFute\LocaleRoute\Locale\Route as LocaleRoute;
use CaribouFute\LocaleRoute\Locale\Url as LocaleUrl;
use Closure;
use Illuminate\Routing\Router as LaravelRouter;

class SubRouter
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
        return $this->makeLocaleRoute('get', $route, $action, $urls);
    }

    public function post($route, $action, array $urls = [])
    {
        return $this->makeLocaleRoute('post', $route, $action, $urls);
    }

    public function put($route, $action, array $urls = [])
    {
        return $this->makeLocaleRoute('put', $route, $action, $urls);
    }

    public function patch($route, $action, array $urls = [])
    {
        return $this->makeLocaleRoute('patch', $route, $action, $urls);
    }

    public function delete($route, $action, array $urls = [])
    {
        return $this->makeLocaleRoute('delete', $route, $action, $urls);
    }

    public function options($route, $action, array $urls = [])
    {
        return $this->makeLocaleRoute('options', $route, $action, $urls);
    }

    public function makeLocaleRoute($method, $route, $action, array $urls = [])
    {
        $group = collect($this->laravelRouter->getGroupStack())->last();
        $locale = $this->localeRoute->getLocale($group['as']);

        return $this->makeRoute($locale, $method, $route, $action, $urls);
    }

    public function makeRoute($locale, $method, $route, $action, array $urls = [])
    {
        $url = $this->localeUrl->getUnlocaleRouteUrl($locale, $route, $urls);
        $action = $this->addRouteToAction($route, $action);

        return $this->laravelRouter->$method($url, $action);
    }

    public function addRouteToAction($route, $action)
    {
        $action = is_string($action) || is_a($action, Closure::class) ? ['uses' => $action] : $action;
        $action['as'] = $route;

        return $action;
    }
}
