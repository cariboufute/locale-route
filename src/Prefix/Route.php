<?php

namespace CaribouFute\LocaleRoute\Prefix;

use CaribouFute\LocaleRoute\Locales;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router as IlluminateRouter;
use Illuminate\Routing\UrlGenerator;
use InvalidArgumentException;

class Route extends Base
{
    protected $separator = '.';
    protected $url;
    protected $router;
    protected $app;

    public function __construct(Locales $locales, UrlGenerator $url, IlluminateRouter $router, Application $app)
    {
        parent::__construct($locales);
        $this->url = $url;
        $this->router = $router;
        $this->app = $app;
    }

    public function localeRoute(
        $locale = null,
        $name = null,
        $parameters = [],
        $absolute = true
    ) {
        $locale = $locale ?: $this->app->getLocale();
        $name = $name ?: $this->getCurrentRouteName();
        $localeName = $this->switchLocale($locale, $name);

        return $this->getLocaleOrNotLocaleRouteUrl($localeName, $name, $parameters, $absolute);
    }

    protected function getLocaleOrNotLocaleRouteUrl(
        $localeName = null,
        $name = null,
        $parameters = [],
        $absolute = true
    ) {
        try {
            $url = $this->url->route($localeName, $parameters, $absolute);
        } catch (InvalidArgumentException $e) {
            $url = $this->url->route($name, $parameters, $absolute);
        }

        $url = rtrim($url, '?');

        return $url;
    }

    public function getCurrentRouteName()
    {
        $currentRoute = $this->router->current();
        $currentRouteName = $currentRoute ? $currentRoute->getName() : '';

        return $currentRouteName;
    }

    public function otherLocale($locale = null, $parameters = null, $absolute = true)
    {
        $name = $this->getCurrentRouteName();
        $parameters = $parameters ?: $this->getCurrentRouteParameters();

        return $this->localeRoute($locale, $name, $parameters, $absolute);
    }

    public function getCurrentRouteParameters()
    {
        $currentRoute = $this->router->current();
        $currentRouteParameters = $currentRoute ? $currentRoute->parameters() : [];

        return $currentRouteParameters;
    }

    public function otherRoute($name, $parameters = null, $absolute = true)
    {
        return $this->localeRoute(null, $name, $parameters, $absolute);
    }
}
