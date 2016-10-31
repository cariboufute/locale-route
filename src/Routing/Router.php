<?php

namespace CaribouFute\LocaleRoute\Routing;

use Illuminate\Routing\Router;
use Illuminate\Translation\Translator;
use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
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
        $this->makeMethodUris('get', $uris, $action);
    }

    public function post($uris, $action = null)
    {
        $this->makeMethodUris('post', $uris, $action);
    }

    public function put($uris, $action = null)
    {
        $this->makeMethodUris('put', $uris, $action);
    }

    public function patch($uris, $action = null)
    {
        $this->makeMethodUris('patch', $uris, $action);
    }

    public function delete($uris, $action = null)
    {
        $this->makeMethodUris('delete', $uris, $action);
    }

    public function options($uris, $action = null)
    {
        $this->makeMethodUris('options', $uris, $action);
    }

    public function makeMethodUris($method, $uris, $action = null)
    {
        foreach ($uris as $locale => $uri) {
            $this->makeMethodUri($method, $locale, $uri, $action);
        }
    }

    protected function makeMethodUri($method, $locale, $uri, $action)
    {
        $localeAction = $this->makeLocaleAction($locale, $action);
        $this->makeIlluminateRoute($method, $locale, $uri, $localeAction);
    }

    protected function makeLocaleAction($locale, $action)
    {
        $localeAction = $action;

        if ($this->actionHasLocaleArrayForRoute($action)) {
            $localeAction['as'] = $action['as'][$locale];
        }

        return $localeAction;
    }

    protected function actionHasLocaleArrayForRoute($action)
    {
        return isset($action['as']) && is_array($action['as']);
    }

    protected function makeIlluminateRoute($method, $locale, $uri, $action)
    {
        $localeUri = $this->getLocaleUri($locale, $uri);
        $setLocaleName = SetSessionLocale::class . ':' . $locale;

        return $this->router
                    ->$method($localeUri, $action)
                    ->middleware($setLocaleName);
    }

    protected function getLocaleUri($locale, $uri)
    {
        return Config::get('localeroute.add_locale_to_uri') ? $locale . '/' . $uri : $uri;
    }

    public function getRoute($route, $action)
    {
        $this->makeMethodRoutes('get', $route, $action);
    }

    public function postRoute($route, $action)
    {
        $this->makeMethodRoutes('post', $route, $action);
    }

    public function putRoute($route, $action)
    {
        $this->makeMethodRoutes('put', $route, $action);
    }

    public function patchRoute($route, $action)
    {
        $this->makeMethodRoutes('patch', $route, $action);
    }

    public function deleteRoute($route, $action)
    {
        $this->makeMethodRoutes('delete', $route, $action);
    }

    public function optionsRoute($route, $action)
    {
        $this->makeMethodRoutes('options', $route, $action);
    }


    public function makeMethodRoutes($method, $route, $action)
    {
        $locales = Config::get('localeroute.locales');

        foreach ($locales as $locale) {
            $localeRoute = $locale . '.' . $route;
            $uri = $this->translator->get('routes.' . $route, [], $locale);
            $action = $this->addLocaleRouteToAction($locale, $route, $action);
            $this->makeIlluminateRoute($method, $locale, $uri, $action);
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
