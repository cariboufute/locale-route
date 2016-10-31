<?php

namespace CaribouFute\LocaleRoute\Routing;

use Illuminate\Routing\Router;
use Illuminate\Translation\Translator;
use CaribouFute\LocaleRoute\Http\Middleware\SetLocale;
use Config;

class Router
{
    protected $router;


    public function __construct(Router $router, Translator $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    public function get($uris, $action = null)
    {
        $this->makeRoutes('get', $uris, $action);
    }

    public function post($uris, $action = null)
    {
        $this->makeRoutes('post', $uris, $action);
    }

    public function put($uris, $action = null)
    {
        $this->makeRoutes('put', $uris, $action);
    }

    public function patch($uris, $action = null)
    {
        $this->makeRoutes('patch', $uris, $action);
    }

    public function delete($uris, $action = null)
    {
        $this->makeRoutes('delete', $uris, $action);
    }

    public function options($uris, $action = null)
    {
        $this->makeRoutes('options', $uris, $action);
    }

    public function getRoute($route, $action)
    {
        $this->makeLocaleRoutes('get', $route, $action);
    }

    public function postRoute($route, $action)
    {
        $this->makeLocaleRoutes('post', $route, $action);
    }

    public function putRoute($route, $action)
    {
        $this->makeLocaleRoutes('put', $route, $action);
    }

    public function patchRoute($route, $action)
    {
        $this->makeLocaleRoutes('patch', $route, $action);
    }

    public function deleteRoute($route, $action)
    {
        $this->makeLocaleRoutes('delete', $route, $action);
    }

    public function optionsRoute($route, $action)
    {
        $this->makeLocaleRoutes('options', $route, $action);
    }

    public function makeRoutes($method, $uris, $action = null)
    {
        foreach ($uris as $locale => $uri) {
            $localeAction = $this->makeLocaleAction($locale, $action);
            $this->makeRoute($method, $locale, $uri, $localeAction);
        }
    }

    protected function makeLocaleAction($locale, $action)
    {
        $localeAction = $action;

        if (isset($action['as']) && is_array($action['as'])) {
            $localeAction['as'] = $action['as'][$locale];
        }

        return $localeAction;
    }

    protected function makeRoute($method, $locale, $uri, $action)
    {
        $localeUri = $locale . '/' . $uri;
        $setLocaleName = SetLocale::class . ':' . $locale;

        return $this->router
                    ->$method($localeUri, $action)
                    ->middleware($setLocaleName);
    }

    public function makeLocaleRoutes($method, $route, $action)
    {
        $locales = Config::get('localeroute.locales');

        foreach ($locales as $locale) {
            $localeRoute = $locale . '.' . $route;
            $uri = $this->translator->get('routes.' . $route, [], $locale);
            $action = $this->addLocaleRouteToAction($locale, $route, $action);
            $this->makeRoute($method, $locale, $uri, $action);
        }
    }

    public function addLocaleRouteToAction($locale, $route, $action)
    {
        $localeRoute = $locale.'.'.$route;

        if (is_array($action)) {
            $action['as'] = $localeRoute;
        } else { 
            $action = ['as' => $localeRoute, 'uses' => $action];
        }

        return $action;
    }
}
