<?php

namespace CaribouFute\LocaleRoute\Routing;

use Illuminate\Routing\Router;
use Illuminate\Translation\Translator;
use CaribouFute\LocaleRoute\Middleware\SetSessionLocale;
use CaribouFute\LocaleRoute\Routing\Url;
use Config;

class Router
{
    protected $router;
    protected $translator;
    protected $uri;


    public function __construct(Router $router, Translator $translator, Url $uri)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->uri = $uri;
    }

    public function get($uris, $action = null)
    {
        $this->makeMethodUrls('get', $uris, $action);
    }

    public function post($uris, $action = null)
    {
        $this->makeMethodUrls('post', $uris, $action);
    }

    public function put($uris, $action = null)
    {
        $this->makeMethodUrls('put', $uris, $action);
    }

    public function patch($uris, $action = null)
    {
        $this->makeMethodUrls('patch', $uris, $action);
    }

    public function delete($uris, $action = null)
    {
        $this->makeMethodUrls('delete', $uris, $action);
    }

    public function options($uris, $action = null)
    {
        $this->makeMethodUrls('options', $uris, $action);
    }

    public function makeMethodUrls($method, $uris, $action = null)
    {
        foreach ($uris as $locale => $uri) {
            $this->makeMethodUrl($method, $locale, $uri, $action);
        }
    }

    protected function makeMethodUrl($method, $locale, $uri, $action)
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
        $localeUrl = $this->uri->addLocale($locale, $uri);
        $setLocaleName = SetSessionLocale::class . ':' . $locale;

        return $this->router
                    ->$method($localeUrl, $action)
                    ->middleware($setLocaleName);
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
            $this->makeMethodRoute($locale, $method, $route, $action);
        }
    }

    public function makeMethodRoute($locale, $method, $route, $action)
    {
        $localeRoute = $locale . '.' . $route;
        $uri = $this->translator->get('routes.' . $route, [], $locale);
        $action = $this->addLocaleRouteToAction($locale, $route, $action);
        $this->makeIlluminateRoute($method, $locale, $uri, $action);
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
