<?php

namespace CaribouFute\LocaleRoute\Routing;

use Illuminate\Routing\Router;
use CaribouFute\LocaleRoute\Http\Middleware\SetLocale;

class Router
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
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
    

    public function makeRoutes($method, $uris, $action = null)
    {
        foreach ($uris as $locale => $uri) {
            $this->makeRoute($method, $locale, $uri, $action);
        }
    }

    protected function makeRoute($method, $locale, $uri, $action)
    {
        $localeUri = $locale . '/' . $uri;
        $setLocaleName = SetLocale::class . ':' . $locale;

        return $this->router
                    ->$method($localeUri, $action)
                    ->middleware($setLocaleName);
    }
}
